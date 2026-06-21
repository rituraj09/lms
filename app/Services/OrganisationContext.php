<?php

namespace App\Services;

class OrganisationContext
{
    private const SESSION_KEY = 'organisation_context';

    /**
     * ✅ Set organisation context
     */
    public static function set(?int $organisationId): void
    {
        if ($organisationId) {
            session([self::SESSION_KEY => $organisationId]);
        } else {
            session()->forget(self::SESSION_KEY);
        }
    }

    /**
     * ✅ Get organisation ID (not geatId!)
     */
    public static function getId(): ?int
    {
        return session(self::SESSION_KEY);
    }

    /**
     * Check if organisation context is active
     */
    public static function isActive(): bool
    {
        return !is_null(self::getId());
    }

    /**
     * Get current organisation object (if you need it)
     */
    public static function get()
    {
        $orgId = self::getId();
        if ($orgId) {
            return \App\Models\Master\Organisation::find($orgId);
        }
        return null;
    }

    /**
     * Clear organisation context
     */
    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
