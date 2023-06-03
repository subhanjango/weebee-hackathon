<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'date',
        'start_time',
        'end_time',
        'user_id'
    ];

    public function service()
    {
        return $this->belongsTo(ProviderService::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookASlot($data)
    {
        $provider_service_model_obj = new ProviderService();

        $slotAvailable = $provider_service_model_obj->isSlotAvailable($data['service_id'], $data['date'], $data['start_time'], count($data['users']));

        if (!$slotAvailable['status']) {
            return $slotAvailable;
        }

        $bookings = [];

        foreach ($data['users'] as $user) {

            $user = User::create([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email']
            ]);


            $bookings[] = $this->create([
                'service_id' => $data['service_id'],
                'date' => $data['date'],
                'start_time' => $slotAvailable['slots']['slot_start_time'],
                'end_time' => $slotAvailable['slots']['slot_end_time'],
                'user_id' => $user->id
            ])->id;
        }

        $response['status'] = true;

        $response['data'] = $this->with([
            'service',
            'user'
        ])->whereIn('id', $bookings)->get();

        return $response;
    }

}
