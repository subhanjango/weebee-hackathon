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
        Schema::create('provider_service_bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id')->unsigned();
            $table->string('date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('primary_user_first_name');
            $table->string('primary_user_last_name');
            $table->string('primary_user_email_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_service_bookings');
    }
};
