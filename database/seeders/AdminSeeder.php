<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'telephone' => '770000101',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role_id' => 2,
        ]);
    }
}
