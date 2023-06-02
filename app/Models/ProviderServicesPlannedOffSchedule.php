<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderServicesPlannedOffSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'reason',
        'start_time',
        'end_time',
        'full_day_off',
        'service_id'
    ];
}
