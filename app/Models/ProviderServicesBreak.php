<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderServicesBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'day_number',
        'start_time',
        'end_time',
        'service_id'
    ];
}
