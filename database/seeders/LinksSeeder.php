<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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

        $links = [
            'view dashboard link',
            'view settings link',
            'view own-profile link',
            'view access-management link',
            'view groups link',
            'view users link',
            'view roles link',
            'view permissions link'
        ];

        foreach ($links as $link) {
            Permission::create([
                'name' => $link,
                "guard_name" => "web",
            ]);
        }
    }
}
