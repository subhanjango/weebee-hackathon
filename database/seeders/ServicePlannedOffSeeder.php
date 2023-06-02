<?php

namespace Database\Seeders;

use App\Models\ProviderService;
use App\Models\ProviderServicesPlannedOffSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ServicePlannedOffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $men_haircut_service = ProviderService::where('name' , 'Men Haircut')->first();

        ProviderServicesPlannedOffSchedule::create([
            'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'full_day_off' => true,
            'reason' => 'Public Holiday',
            'service_id' => $men_haircut_service->id
        ]);

        $woman_haircut_service = ProviderService::where('name' , 'Woman Haircut')->first();

        ProviderServicesPlannedOffSchedule::create([
            'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'full_day_off' => true,
            'reason' => 'Public Holiday',
            'service_id' => $woman_haircut_service->id
        ]);
    }
}
