<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\OrganisationContext;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganisationContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        // If not super_admin, check organisation access
        if (!$admin->isSuperAdmin() && OrganisationContext::isActive()) {
            // ✅ FIXED: geatId() → getId()
            $orgId = OrganisationContext::getId();

            if (!$admin->hasOrganisationAccess($orgId)) {
                OrganisationContext::clear();
                return redirect()->route('admin.home')
                    ->with('error', 'You do not have access to this organisation.');
            }
        }

        return $next($request);
    }
}
