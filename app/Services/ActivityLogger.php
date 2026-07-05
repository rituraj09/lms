<?php
// app/Services/ActivityLogger.php

namespace App\Services;

use App\Jobs\LogActivityJob;
use Illuminate\Http\Request;

class ActivityLogger
{
    // Call this from anywhere - Livewire or API Controller
    public static function log(
        int    $userId,
        string $userType,
        string $action,
        array  $extra = []
    ): void {
        $request = request();

        LogActivityJob::dispatch(
            userId:   $userId,
            userType: $userType,
            action:   $action,
            data: array_merge([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ], $extra)
        )->onQueue('activity-logs');  // separate queue for logs
    }
}
