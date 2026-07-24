<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemoRequestFormRequest;
use App\Models\DemoRequest;
use App\Mail\DemoRequestReceived;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DemoRequestController extends Controller
{
    public function store(DemoRequestFormRequest $request)
    {
        try {
            $demoRequest = DemoRequest::create([
                'full_name'      => $request->full_name,
                'institution'    => $request->institution,
                'role'           => $request->role,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'preferred_slot' => $request->preferred_slot,
                'message'        => $request->message,
            ]);

            // Optional: Send confirmation email to user
            try {
                Mail::to($demoRequest->email)->send(new DemoRequestReceived($demoRequest));
            } catch (\Exception $e) {
                Log::error('Demo request mail failed: ' . $e->getMessage());
            }

            // Optional: Notify admin
            try {
                Mail::to(config('mail.admin_address', 'admin@mindshiksha.com'))
                    ->send(new DemoRequestReceived($demoRequest, true));
            } catch (\Exception $e) {
                Log::error('Admin notification mail failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your request has been received. Our team will reach out within 1 business day.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Demo request submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
}
