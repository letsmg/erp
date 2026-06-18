<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() < 5) {
            User::factory()->count(5)->create();
        }

        $firstName = 'Admin';
        $lastName = 'Sistema';
        $email = 'admin@teste.com';

        User::updateOrCreate(
            ['email_hash' => hash('sha256', $email)],
            [
                'password' => Hash::make('Mudar@123'),
                'first_name_hash' => hash('sha256', $firstName),
                'first_name_encrypted' => Crypt::encryptString($firstName),
                'last_name_hash' => hash('sha256', $lastName),
                'last_name_encrypted' => Crypt::encryptString($lastName),
                'display_name' => "{$firstName} {$lastName}",
                'email_hash' => hash('sha256', $email),
                'email_encrypted' => Crypt::encryptString($email),
                'access_level' => 1,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}