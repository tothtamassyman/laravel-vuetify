<?php

namespace App\Services;

use App\Models\User;

class AbilityService
{
    /**
     * Get abilities for the authenticated user.
     *
     * @param  User  $user
     * @return mixed
     */
    public function getUserAbilities(User $user): mixed
    {
        $directPermissions = $user->getDirectPermissions();
        $rolePermissions = $user->getPermissionsViaRoles();

        $allPermissions = $directPermissions->merge($rolePermissions)->unique('id');

        return $allPermissions->map(function ($permission) {
            $parts = explode(' ', $permission->name, 2);

            return [
                'action' => $parts[0],
                'subject' => $parts[1] ?? null,
            ];
        });
    }
}