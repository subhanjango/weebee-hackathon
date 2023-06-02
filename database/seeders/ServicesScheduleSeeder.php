<?php

namespace Database\Seeders;

use App\Models\ProviderService;
use App\Models\ProviderServicesSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $days = config('constants.days');

        $men_haircut_service = ProviderService::where('name' , 'Men Haircut')->first();

        $schedule = [
            [
                'day_number' => $days['MONDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['TUESDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['WEDNESDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['THURSDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['FRIDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['SATURDAY'],
                'start_time' => '10:00',
                'end_time' => '22:00',
                'service_id' => $men_haircut_service->id
            ],
            [
                'day_number' => $days['SUNDAY'],
                'day_off' => true,
                'service_id' => $men_haircut_service->id
            ],
        ];

        foreach ($schedule as $value) {
            ProviderServicesSchedule::create($value);
        }

        $woman_haircut_service = ProviderService::where('name' , 'Woman Haircut')->first();

        $schedule = [
            [
                'day_number' => $days['MONDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['TUESDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['WEDNESDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['THURSDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['FRIDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['SATURDAY'],
                'start_time' => '10:00',
                'end_time' => '22:00',
                'service_id' => $woman_haircut_service->id
            ],
            [
                'day_number' => $days['SUNDAY'],
                'day_off' => true,
                'service_id' => $woman_haircut_service->id
            ],
        ];

        foreach ($schedule as $value) {
            ProviderServicesSchedule::create($value);
        }
    }
}
