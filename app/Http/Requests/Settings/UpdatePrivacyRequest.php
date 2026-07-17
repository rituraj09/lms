<?php
// app/Http/Requests/Settings/UpdatePrivacyRequest.php
namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrivacyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'profile_visibility'  => ['sometimes', 'in:public,private'],
            'show_on_leaderboard' => ['sometimes', 'boolean'],
            'two_factor_enabled'  => ['sometimes', 'boolean'],
        ];
    }
}
