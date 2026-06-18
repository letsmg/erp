<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class SingleUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'first_name_hash' => hash('sha256', 'Admin'),
                'first_name_encrypted' => Crypt::encryptString('Admin'),
                'last_name_hash' => hash('sha256', 'Sistema'),
                'last_name_encrypted' => Crypt::encryptString('Sistema'),
                'display_name' => 'Admin Sistema',
                'email_hash' => hash('sha256', '1@1.com'),
                'email_encrypted' => Crypt::encryptString('1@1.com'),
                'password' => \Illuminate\Support\Facades\Hash::make('Mudar@123'),
                'access_level' => 1,
                'is_active' => true,
            ]
        );
        User::updateOrCreate(
            ['username' => 'usuario'],
            [
                'first_name_hash' => hash('sha256', 'Usuario'),
                'first_name_encrypted' => Crypt::encryptString('Usuario'),
                'last_name_hash' => hash('sha256', 'Padrao'),
                'last_name_encrypted' => Crypt::encryptString('Padrao'),
                'display_name' => 'Usuario Padrao',
                'email_hash' => hash('sha256', '2@1.com'),
                'email_encrypted' => Crypt::encryptString('2@1.com'),
                'password' => \Illuminate\Support\Facades\Hash::make('Mudar@123'),
                'access_level' => 0,
                'is_active' => true,
            ]
        );
        User::updateOrCreate(
            ['username' => 'cliente'],
            [
                'first_name_hash' => hash('sha256', 'Cliente'),
                'first_name_encrypted' => Crypt::encryptString('Cliente'),
                'last_name_hash' => hash('sha256', 'Implementar'),
                'last_name_encrypted' => Crypt::encryptString('Implementar'),
                'display_name' => 'Cliente Implementar',
                'email_hash' => hash('sha256', '3@1.com'),
                'email_encrypted' => Crypt::encryptString('3@1.com'),
                'password' => \Illuminate\Support\Facades\Hash::make('Mudar@123'),
                'access_level' => 2,
                'is_active' => true,
            ]
        );

        $this->command->info("Usuário verificado/criado!");
    }
}