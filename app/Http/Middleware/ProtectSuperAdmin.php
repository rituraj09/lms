<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;

class ProtectSuperAdmin
{
    /**
     * Prevent non-super admins from modifying super_admin
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the 'admin' route parameter
        $adminParam = $request->route('admin');

        // ✅ Handle both cases: Model (from route binding) or ID
        if ($adminParam instanceof Admin) {
            $targetAdmin = $adminParam;
        } elseif (is_numeric($adminParam)) {
            $targetAdmin = Admin::find($adminParam);
        } else {
            $targetAdmin = null;
        }

        // ✅ Check if target is super admin
        if ($targetAdmin && $targetAdmin->isSuperAdmin()) {

            // ✅ Current user must also be super admin
            $currentUser = auth('admin')->user();

            if (!$currentUser || !$currentUser->isSuperAdmin()) {
                abort(403, 'You cannot modify Super Admin.');
            }
        }

        return $next($request);
    }
}
