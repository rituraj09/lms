<?php

namespace App\Traits;

use App\Models\Master\Organisation;

trait WithOrganisationAccess
{
    public Organisation $organisation;

    /**
     * Check organisation access and permission
     */
    protected function checkOrgAccess(int $organisationId, string $permission): void
    {
        $this->organisation = Organisation::findOrFail($organisationId);

        $user = auth('admin')->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Check if user has access to organisation
        if (!$user->isSuperAdmin() && !$user->hasOrganisationAccess($organisationId)) {
            abort(403, 'You do not have access to this organisation');
        }

        // Check organisation-level permission
        if (!$user->isSuperAdmin() && !$user->hasOrgPermission($permission, $organisationId)) {
            abort(403, "You do not have permission: {$permission}");
        }

        // Set current organisation context
        $user->setCurrentOrganisation($organisationId);
    }
}
