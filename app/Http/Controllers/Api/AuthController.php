<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login Student
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Find user by email
        $user = User::with('details')
            ->where('email', $request->email)
            ->first();

        // Check user exists and password matches
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account is not active. Please contact support.',
            ], 403);
        }

        // Revoke old tokens (optional - single session)
        $user->tokens()->delete();

        // Create new Sanctum token
        $token = $user->createToken('student-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => new UserResource($user),
        ], 200);
    }

    /**
     * Register Student
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'pending', // New registrations pending approval
        ]);

        // Create empty user details
        $user->details()->create([
            'first_name' => explode(' ', $request->name)[0],
            'last_name'  => explode(' ', $request->name)[1] ?? '',
        ]);

        // Load details relationship
        $user->load('details');

        // Create token
        $token = $user->createToken('student-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'token'   => $token,
            'user'    => new UserResource($user),
        ], 201);
    }

    /**
     * Get Authenticated User
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('details');

        return response()->json([
            'user' => new UserResource($user),
        ], 200);
    }

    /**
     * Logout Student
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }
}
