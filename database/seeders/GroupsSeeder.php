<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::create([
            'id' => 1,
            'name' => env('DEFAULT_GROUP_NAME', 'Default'),
            'description' => env('DEFAULT_GROUP_DESCRIPTION', 'Default group'),
            'user_id' => 1,
        ]);
    }
}
