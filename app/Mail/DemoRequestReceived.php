<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DemoRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $demoRequest;
    public $isAdmin;

    public function __construct(DemoRequest $demoRequest, bool $isAdmin = false)
    {
        $this->demoRequest = $demoRequest;
        $this->isAdmin     = $isAdmin;
    }

    public function build()
    {
        $subject = $this->isAdmin
            ? '🔔 New Demo Request Received'
            : '✅ Your Demo Request - MindShiksha';

        return $this->subject($subject)
            ->view('website.layouts.demo-request', [
                'demoRequest' => $this->demoRequest,
                'isAdmin'     => $this->isAdmin,
            ]);
    }
}
