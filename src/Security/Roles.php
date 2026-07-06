<?php

declare(strict_types=1);

namespace App\Security;

final class Roles
{
    public const MANAGER = 'ROLE_MANAGER';
    public const ADMIN = 'ROLE_ADMIN';

    public const ASSIGNABLE = [
        self::MANAGER => 'Fermier',
        self::ADMIN => 'Administrateur',
    ];

    public const LABELS = [
        self::MANAGER => 'Fermier',
        self::ADMIN => 'Administrateur',
    ];

    public static function label(string $role): string
    {
        return self::LABELS[$role] ?? $role;
    }

    public static function primaryLabel(array $roles): string
    {
        foreach ([self::ADMIN, self::MANAGER] as $role) {
            if (in_array($role, $roles, true)) {
                return self::label($role);
            }
        }

        return 'Client';
    }
}
