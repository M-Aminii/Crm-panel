<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(1)
            ->create()
            ->each(function ($user) {
                $user->assignRole('super-admin');
            });

        User::factory()
            ->count(2)
            ->create()
            ->each(function ($user) {
                $user->assignRole('system-admin');
            });

        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) {
                $user->assignRole('member');
            });
            }
}

