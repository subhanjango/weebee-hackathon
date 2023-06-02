<?php

namespace Database\Seeders;

use App\Models\ProviderService;
use App\Models\ProviderServicesBreak;
use Illuminate\Database\Seeder;

class ServicesScheduleBreakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = config('constants.days');

        $men_haircut_service = ProviderService::where('name' , 'Men Haircut')->first();

        $breaks = [
            [
                'day_number' => $days['MONDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['TUESDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['WEDNESDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['THURSDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['FRIDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['SATURDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['MONDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['TUESDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['WEDNESDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['THURSDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['FRIDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['SATURDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $men_haircut_service->id
            ],
        ];

        foreach ($breaks as $break) {
            ProviderServicesBreak::create($break);
        }

        $woman_haircut_service = ProviderService::where('name' , 'Woman Haircut')->first();

        $breaks = [
            [
                'day_number' => $days['MONDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['TUESDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['WEDNESDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['THURSDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['FRIDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['SATURDAY'],
                'start_time' => '12:00',
                'end_time' => '13:00',
                'reason' => 'Lunch Time',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['MONDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['TUESDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['WEDNESDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['THURSDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['FRIDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['SATURDAY'],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'reason' => 'Cleaning Break',
                'service_id' => $woman_haircut_service->id
            ],
        ];

        foreach ($breaks as $break) {
            ProviderServicesBreak::create($break);
        }
    }
}
