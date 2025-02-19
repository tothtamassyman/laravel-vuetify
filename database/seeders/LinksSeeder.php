<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class LinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard link',
            'view settings link',
            'view own-profile link',
            'view access-management link',
            'view groups link',
            'view users link',
            'view roles link',
            'view permissions link'
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'description' => ucwords($permission),
                "guard_name" => config('auth.defaults.guard'),
            ]);
        }
    }
}
