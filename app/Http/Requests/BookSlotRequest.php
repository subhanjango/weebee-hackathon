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
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'primary_user_first_name' => 'required',
            'primary_user_last_name' => 'required',
            'primary_user_email_address' => 'required|email',
            'secondary_users_active' => 'required|boolean',
            'secondary_users' => 'required_if:secondary_users_active,==,true|array',
            'secondary_users.*.clone_primary_user' => 'required_if:secondary_users_active,==,true|boolean',
            'secondary_users.*.email' => 'required_if:secondary_users.*.clone_primary_user,==,false||email',
            'secondary_users.*.first_name' => 'required_if:secondary_users.*.clone_primary_user,==,false',
            'secondary_users.*.last_name' => 'required_if:secondary_users.*.clone_primary_user,==,false',
            'secondary_users_count' => 'nullable',
        ];
    }

    public function all($keys = null)
    {
        if ($this->request->has('secondary_users')) {
            $this->merge([
                'secondary_users_count' => count($this->get('secondary_users'))
            ]);
        } else if ($this->request->has('secondary_users_active') && !$this->request->get('secondary_users_active')) {
            $this->merge([
                'secondary_users_count' => 0
            ]);
        }

        return parent::all();
    }
}
