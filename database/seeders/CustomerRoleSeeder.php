<?php

namespace Database\Seeders;

use App\Models\CustomerRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = [
            ['name' => 'همکار', 'level' => 1],
            ['name' => 'همکار', 'level' => 2],
            ['name' => 'همکار', 'level' => 3],
            ['name' => 'سازنده', 'level' => 1],
            ['name' => 'سازنده', 'level' => 2],
            ['name' => 'سازنده', 'level' => 3],
            ['name' => 'طراح', 'level' => 1],
            ['name' => 'طراح', 'level' => 2],
            ['name' => 'طراح', 'level' => 3],
            ['name' => 'معمار', 'level' => 1],
            ['name' => 'معمار', 'level' => 2],
            ['name' => 'معمار', 'level' => 3],
            ['name' => 'مصرف کننده', 'level' => 1],
            ['name' => 'مصرف کننده', 'level' => 2],
            ['name' => 'مصرف کننده', 'level' => 3],
        ];

        foreach ($roles as $role) {
            CustomerRole::create($role);
        }
    }
}
