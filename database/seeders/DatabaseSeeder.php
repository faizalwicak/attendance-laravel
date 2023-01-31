<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin'),
            'role' => 'SUPERADMIN',
        ]);
    }
}
