<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'id' => 1,
            'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
            'email' => env('SUPER_ADMIN_EMAIL', 'super_admin@example.com'),
            'password' => bcrypt(env('SUPER_ADMIN_PASSWORD', 'aA12345.')),
        ]);

        $defaultGroup = Group::where('id', 1)->first();

        if ($defaultGroup) {
            $user->groups()->attach($defaultGroup->id);

            $viewLinkPermissions = Permission::where('name', 'LIKE', 'view %')->pluck('name')->toArray();
            if($viewLinkPermissions) {
                setPermissionsTeamId($defaultGroup->id);
                $user->givePermissionTo($viewLinkPermissions);
            }
        }
    }
}
