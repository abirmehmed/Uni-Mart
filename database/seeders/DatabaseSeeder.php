<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@unimart.test'],
            ['name' => 'Admin', 'role' => 'admin', 'password' => bcrypt('password')]
        );

        User::updateOrCreate(
            ['email' => 'cashier@unimart.test'],
            ['name' => 'Cashier', 'role' => 'cashier', 'password' => bcrypt('password')]
        );
    }
}
