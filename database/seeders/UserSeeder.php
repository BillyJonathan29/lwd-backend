<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = [
            ['full_name' => 'Billy Jonathan', 'nim' => '20240140015', 'role' => 'Admin'],
            ['full_name' => 'Dikri Fauzan Amrulloh', 'nim' => '20240140078', 'role' => 'Admin'],
            ['full_name' => 'Masnun Muhaemin', 'nim' => '20240140077', 'role' => 'Admin'],
            ['full_name' => 'Mr. Fatra', 'nim' => '20240140079', 'role' => 'User'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password123'),
                    'role' => $user['role'],
                ]
            );
        }
    }
}
