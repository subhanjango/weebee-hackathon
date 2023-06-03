<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:provider_services,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i|after_or_equal:' . now()->format('H:i'),
            'users' => 'required|array',
            'users.*.first_name' => 'required',
            'users.*.last_name' => 'required',
            'users.*.email' => 'required|email',
        ];
    }
}
