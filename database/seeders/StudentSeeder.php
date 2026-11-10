<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'Alice Wambui',   'email' => 'alice@example.com',   'role' => 'student'],
            ['name' => 'Michael Odhiambo', 'email' => 'michael@example.com', 'role' => 'student'],
            ['name' => 'Mrs. Sarah Kamau', 'email' => 'sarah@example.com',   'role' => 'parent'],
        ];

        foreach ($students as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => $data['role'],
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]
            );
        }
    }
}
