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
        Schema::create('provider_services_breaks', function (Blueprint $table) {
            $table->id();
            $table->string('reason' , 255);
            $table->integer('day_number');
            $table->string('start_time');
            $table->string('end_time');
            $table->integer('service_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_services_breaks');
    }
};
