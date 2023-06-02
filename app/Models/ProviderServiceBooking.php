<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ProviderServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'date',
        'start_time',
        'end_time',
        'primary_user_first_name',
        'primary_user_last_name',
        'primary_user_email_address'
    ];

    public function service()
    {
        return $this->belongsTo(ProviderService::class, 'service_id');
    }

    public function bookASlot($data)
    {
        $provider_service_model_obj = new ProviderService();

        $slotAvailable = $provider_service_model_obj->isSlotAvailable($data['service_id'], $data['date'], $data['start_time'], $data['end_time'], $data['secondary_users_count']);

        if (!$slotAvailable['status']) {
            return $slotAvailable;
        }

        $booking_data[0] = [
            'service_id' => $data['service_id'],
            'date' => $data['date'],
            'start_time' => $slotAvailable['other_slots'][0]['slot_start_time'],
            'end_time' => $slotAvailable['other_slots'][0]['slot_end_time'],
            'primary_user_first_name' => $data['primary_user_first_name'],
            'primary_user_last_name' => $data['primary_user_last_name'],
            'primary_user_email_address' => $data['primary_user_email_address']
        ];

        unset($slotAvailable['other_slots'][0]);

        $slotAvailable['other_slots'] = array_values($slotAvailable['other_slots']);

        if ($data['secondary_users_active']) {
            foreach ($data['secondary_users'] as $key => $secondary_user) {
                if ($secondary_user['clone_primary_user']) {
                    $booking_data[$key + 1] = array_merge($booking_data[0], [
                        'start_time' => $slotAvailable['other_slots'][$key]['slot_start_time'],
                        'end_time' => $slotAvailable['other_slots'][$key]['slot_end_time'],
                        'primary_user_first_name' => $data['primary_user_first_name'],
                        'primary_user_last_name' => $data['primary_user_last_name'],
                        'primary_user_email_address' => $data['primary_user_email_address'],
                    ]);
                } else {
                    $booking_data[$key + 1] = array_merge($booking_data[0], [
                        'start_time' => $slotAvailable['other_slots'][$key]['slot_start_time'],
                        'end_time' => $slotAvailable['other_slots'][$key]['slot_end_time'],
                        'primary_user_first_name' => $secondary_user['first_name'],
                        'primary_user_last_name' => $secondary_user['last_name'],
                        'primary_user_email_address' => $secondary_user['email'],
                    ]);
                }
            }
        }

        $bookings = [];

        foreach ($booking_data as $booking_datum) {
            $bookings[] = $this->create($booking_datum)->id;
        }

        $response['status'] = true;

        $response['data'] = $this->with([
            'service',
        ])->whereIn('id', $bookings)->get();

        return $response;
    }

}
