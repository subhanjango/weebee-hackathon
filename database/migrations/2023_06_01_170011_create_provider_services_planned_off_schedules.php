<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_services_planned_off_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('reason' , 255);
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->integer('service_id')->unsigned();
            $table->boolean('full_day_off')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_services_planned_off_schedules');
    }
};
