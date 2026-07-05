<?php
// app/Jobs/LogActivityJob.php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int    $userId,
        private string $userType,   // 'admin' or 'user'
        private string $action,
        private array  $data = []
    ) {}

    public function handle(): void
    {
        ActivityLog::create([
            'user_id'     => $this->userId,
            'user_type'   => $this->userType,
            'action'      => $this->action,
            'model_type'  => $this->data['model_type'] ?? null,
            'model_id'    => $this->data['model_id'] ?? null,
            'description' => $this->data['description'] ?? null,
            'properties'  => $this->data['properties'] ?? null,
            'ip_address'  => $this->data['ip_address'] ?? null,
            'user_agent'  => $this->data['user_agent'] ?? null,
        ]);
    }
}
