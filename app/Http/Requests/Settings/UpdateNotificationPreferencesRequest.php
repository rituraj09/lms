<?php
// app/Http/Requests/Settings/UpdateNotificationPreferencesRequest.php
namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return collect(config('settings.notification_defaults'))
            ->mapWithKeys(fn ($val, $key) => [$key => ['sometimes', 'boolean']])
            ->all();
    }
}
