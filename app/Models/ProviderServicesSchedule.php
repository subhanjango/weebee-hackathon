<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderServicesSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_number',
        'start_time',
        'end_time',
        'day_off',
        'service_id'
    ];
}
