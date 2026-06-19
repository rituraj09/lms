<?php

namespace App\Services;

use App\Models\Organisation;
use Illuminate\Support\Facades\Session;

class OrganisationContext
{
    const SESSION_KEY = 'active_organisation_id';

    public static function set(int $organisationId): void
    {
        Session::put(self::SESSION_KEY, $organisationId);
    }

    public static function get(): ?Organisation
    {
        $id = Session::get(self::SESSION_KEY);
        if (!$id) return null;
        return Organisation::find($id);
    }

    public static function getId(): ?int
    {
        return Session::get(self::SESSION_KEY);
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public static function isActive(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    public static function check(int $organisationId): bool
    {
        return self::getId() === $organisationId;
    }
}
