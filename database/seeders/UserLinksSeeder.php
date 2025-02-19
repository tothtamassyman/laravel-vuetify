<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class UserLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::find(1);
        $groups = Group::all();
        $permissions = Permission::where('name', 'LIKE', 'view % link')->get();

        foreach ($groups as $group) {
            setPermissionsTeamId($group->id);

            foreach ($permissions as $permission) {
                $user->givePermissionTo($permission->name);
            }
        }
    }
}
