<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Owner User',
                'email' => 'owner@example.com',
                'level' => 2,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'level' => 1,
            ],
            [
                'name' => 'Sales User',
                'email' => 'sales@example.com',
                'level' => 1,
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'level' => 0,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('ims2@123'),
                    'level' => $userData['level'],
                ]
            );
        }
    }
}
