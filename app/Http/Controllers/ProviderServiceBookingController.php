<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookSlotRequest;
use App\Http\Requests\GetSlotRequest;
use App\Models\ProviderService;
use App\Models\ProviderServiceBooking;

class ProviderServiceBookingController extends Controller
{

    public function getSlots(GetSlotRequest $request)
    {
        $provider_service_model_obj = new ProviderService();
        return response()->json($provider_service_model_obj->listSchedule($request->start_date, $request->end_date));
    }

    public function bookSlot(BookSlotRequest $request)
    {
        $provider_service_booking_obj = new ProviderServiceBooking();
        $booking_request = $provider_service_booking_obj->bookASlot($request->validated());
        return response()->json($booking_request, !$booking_request['status'] ? 400 : 200);
    }
}
