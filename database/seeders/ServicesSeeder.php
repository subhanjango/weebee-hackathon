<?php

namespace Database\Seeders;

use App\Models\ProviderService;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Men Haircut',
                'max_booking_future_days_limit' => 7,
                'max_client_per_booking' => 3,
                'minute_duration_per_booking' => 10,
                'prep_break_in_minute' => 5
            ],
            [
                'name' => 'Woman Haircut',
                'max_booking_future_days_limit' => 7,
                'max_client_per_booking' => 3,
                'minute_duration_per_booking' => 60,
                'prep_break_in_minute' => 60
            ],
        ];

        foreach ($services as $service) {
            ProviderService::create($service);
        }
    }
}
