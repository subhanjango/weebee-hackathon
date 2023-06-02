<?php

namespace App\Models;

use App\Helpers\SlotHelper;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ProviderService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'max_booking_future_days_limit',
        'max_client_per_booking',
        'minute_duration_per_booking',
        'prep_break_in_minute'
    ];

    public function breaks()
    {
        return $this->hasMany(ProviderServicesBreak::class, 'service_id');
    }

    public function schedule()
    {
        return $this->hasMany(ProviderServicesSchedule::class, 'service_id');
    }

    public function planned_off()
    {
        return $this->hasOne(ProviderServicesPlannedOffSchedule::class, 'service_id');
    }

    public function bookings()
    {
        return $this->hasMany(ProviderServiceBooking::class, 'service_id');
    }

    public function listSchedule($date_start, $date_end)
    {
        $days = config('constants.days');
        $period = CarbonPeriod::create($date_start, $date_end);
        $dates = [];
        $day_numbers = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
            $day_number = $days[strtoupper($date->format('l'))];
            if (!in_array($day_number, $day_numbers)) {
                $day_numbers[] = $day_number;
            }
        }

        return $this->with([
            'schedule' => function ($query) use ($day_numbers) {
                $query->whereIn('day_number', $day_numbers)->orderBy('day_number');
            },
            'breaks' => function ($query) use ($day_numbers) {
                $query->whereIn('day_number', $day_numbers)->orderBy('day_number');
            },
            'planned_off' => function ($query) use ($dates) {
                $query->whereIn('date', $dates)->orderBy('date');
            },
            'bookings' => function ($query) use ($dates) {
                $query->whereIn('date', $dates)->orderBy('date');
            }])->get();
    }

    public function getServiceForSlots($service_id, $day_number, $date, $start_time, $end_time)
    {
        return $service = $this->with([
            'schedule' => function ($query) use ($day_number) {
                $query->where('day_number', $day_number);
            },
            'breaks' => function ($query) use ($day_number, $start_time, $end_time) {
                $query->where('day_number', $day_number)
                    ->where(function ($query) use ($start_time, $end_time) {
                        $query->whereBetween('start_time', [$start_time, $end_time]);
                        $query->orWhereBetween('start_time', [$start_time, $end_time]);
                    });
            },
            'planned_off' => function ($query) use ($date, $start_time, $end_time) {
                $query->where('date', $date)
                    ->where(function ($query) use ($start_time, $end_time) {
                        $query->whereBetween('start_time', [$start_time, $end_time]);
                        $query->orWhereBetween('end_time', [$start_time, $end_time]);
                        $query->orWhere('full_day_off', 1);
                    });
            },
            'bookings' => function ($query) use ($date, $start_time, $end_time) {
                $query->where('date', $date)
                    ->where(function ($query) use ($start_time, $end_time) {
                        $query->whereBetween('start_time', [$start_time, $end_time]);
                        $query->orWhereBetween('end_time', [$start_time, $end_time]);
                    });
            }
        ])->find($service_id);
    }


    public function isSlotAvailable($service_id, $date, $start_time, $end_time, $secondary_user_count)
    {
        $day_number = config('constants.days')[strtoupper(Carbon::create($date)->format('l'))];

        $service = $this->getServiceForSlots($service_id, $day_number, $date, $start_time, $end_time);

        if ($service->max_client_per_booking < ($secondary_user_count) + 1) {
            return ['status' => false, 'reason' => 'User limit exceeded'];
        }

        if ($service->max_booking_future_days_limit < SlotHelper::getDatesDiff(date('Y-m-d'), $date)) {
            return ['status' => false, 'reason' => 'Booking for future can only be done under ' . $service->max_booking_future_days_limit . ' days'];
        }

        $schedule = isset($service->schedule[0]) ? $service->schedule[0]->toArray() : [];

        $schedule_check = $this->scheduleCheck($schedule, $service);

        if (!$schedule_check['status']) {
            return $schedule_check;
        }

        $slots = SlotHelper::createSlots($schedule['start_time'], $schedule['end_time'], $service->minute_duration_per_booking, $service->prep_break_in_minute);

        $slots = collect($slots);

        $slotAvailable = $slots->where('slot_start_time', $start_time)->where('slot_end_time', $end_time)->first();

        if (!$slotAvailable) {
            return ['status' => false, 'reason' => 'Invalid slot'];
        }

        $other_slots[0] = [
            'slot_start_time' => $start_time,
            'slot_end_time' => $end_time
        ];
        $error_slot = [];
        $initial_slot = $slotAvailable;
        for ($i = 0; $i < $secondary_user_count; $i++) {
            $slot = $slots->where('slot_start_time', '>', $initial_slot['slot_start_time'])->where('slot_end_time', '>', $initial_slot['slot_end_time'])->first();
            if (!$slot) {
                return ['status' => false, 'reason' => 'No slot available for your ' . ($secondary_user_count + 1) . ' secondary user'];
            }

            $this->getServiceForSlots($service_id, $day_number, $date, $slot['slot_start_time'], $slot['slot_end_time']);
            $schedule = isset($service->schedule[0]) ? $service->schedule[0]->toArray() : [];
            $schedule_check = $this->scheduleCheck($schedule, $service);
            if (!$schedule_check['status']) {
                $error_slot = $schedule_check;
                break;
            }
            $other_slots[$i + 1] = $slot;
            $initial_slot = $slot;
        }

        if (count($error_slot)) {
            return $error_slot;
        }

        return [
            'status' => true,
            'other_slots' => $other_slots
        ];
    }

    private function scheduleCheck($schedule, $service)
    {
        if (!count($schedule) || $schedule['day_off']) {
            return ['status' => false, 'reason' => 'Day off'];
        }

        if ($service->planned_off) {
            return ['status' => false, 'reason' => $service->planned_off->reason];
        }

        if (isset($service->breaks[0])) {
            return ['status' => false, 'reason' => $service->breaks[0]->reason];
        }

        if (isset($service->bookings[0])) {
            return ['status' => false, 'reason' => 'Already booked'];
        }

        return ['status' => true];
    }
}
