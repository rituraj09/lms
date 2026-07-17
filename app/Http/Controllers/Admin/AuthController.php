<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string', // email or mobile
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->login)
            ->orWhere('mobile', $request->login)
            ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'login' => ['Invalid credentials.'],
            ]);
        }

        if ($admin->status !== 'active') {
            throw ValidationException::withMessages([
                'login' => ['Your account is ' . $admin->status . '.'],
            ]);
        }

        // ── Permission gate: only super_admin OR has assessment create/view permission ──
        $allowed = $admin->isSuperAdmin()
            || $admin->hasSystemPermission('system.assessment.view')
            || $admin->hasSystemPermission('system.assessment.create');

        if (!$allowed) {
            throw ValidationException::withMessages([
                'login' => ['You do not have permission to access this panel.'],
            ]);
        }

        $token = $admin->createToken('admin-panel')->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => [
                'id'          => $admin->id,
                'name'        => $admin->name,
                'email'       => $admin->email,
                'mobile'      => $admin->mobile,
                'avatar_url'  => $admin->avatar_url,
                'is_super_admin' => $admin->isSuperAdmin(),
                'role'        => $admin->getPrimaryRoleName(),
                'permissions' => $admin->getSystemPermissionNames(),
            ],
        ]);
    }

    public function me(Request $request)
    {
        $admin = $request->user('admin') ?? $request->user();

        return response()->json([
            'admin' => [
                'id'          => $admin->id,
                'name'        => $admin->name,
                'email'       => $admin->email,
                'mobile'      => $admin->mobile,
                'avatar_url'  => $admin->avatar_url,
                'is_super_admin' => $admin->isSuperAdmin(),
                'role'        => $admin->getPrimaryRoleName(),
                'permissions' => $admin->getSystemPermissionNames(),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
