<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemoRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'      => ['required', 'string', 'max:100'],
            'institution'    => ['required', 'string', 'max:150'],
            'role'           => ['required', 'string', 'in:Principal / Head,Teacher / Educator,Administrator,Parent,Other'],
            'email'          => ['required', 'email', 'max:150'],
            'phone'          => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'preferred_slot' => ['nullable', 'date'],
            'message'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'   => 'Please enter your full name.',
            'institution.required' => 'Please enter your institution name.',
            'role.required'        => 'Please select your role.',
            'email.required'       => 'Please enter your email address.',
            'email.email'          => 'Please enter a valid email address.',
            'phone.required'       => 'Please enter your phone number.',
            'phone.regex'          => 'Please enter a valid phone number.',
        ];
    }
}
