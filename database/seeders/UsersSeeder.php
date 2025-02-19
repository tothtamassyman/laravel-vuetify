<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::withoutEvents(function () {
            $user = User::create([
                'id' => 1,
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'email' => env('SUPER_ADMIN_EMAIL', 'super_admin@example.com'),
                'password' => bcrypt(env('SUPER_ADMIN_PASSWORD', 'aA12345.')),
            ]);

            $user->setDetail('default_group_id', 1);
        });
    }
}
