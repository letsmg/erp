<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $email = "cli@$i.com";
            $firstName = "Cliente";
            $lastName = "Teste $i";
            $displayName = "{$firstName} {$lastName}";
            $phone1 = fake()->phoneNumber();
            $documentNumber = fake()->numerify(
                fake()->randomElement(['###########', '##############'])
            );

            Client::firstOrCreate(
                ['email_hash' => hash('sha256', $email)],
                [
                    'password' => Hash::make('Mudar@123'),
                    'first_name_hash' => hash('sha256', $firstName),
                    'first_name_encrypted' => Crypt::encryptString($firstName),
                    'last_name_hash' => hash('sha256', $lastName),
                    'last_name_encrypted' => Crypt::encryptString($lastName),
                    'display_name' => $displayName,
                    'email_hash' => hash('sha256', $email),
                    'email_encrypted' => Crypt::encryptString($email),
                    'name' => $displayName,
                    'email' => $email,
                    'document_type' => fake()->randomElement(['CPF', 'CNPJ']),
                    'document_number' => $documentNumber,
                    'document_hash' => hash('sha256', $documentNumber),
                    'document_encrypted' => Crypt::encryptString($documentNumber),
                    'contact1' => fake()->sentence(),
                    'phone1' => $phone1,
                    'phone1_hash' => hash('sha256', $phone1),
                    'phone1_encrypted' => Crypt::encryptString($phone1),
                    'state_registration' => fake()->optional(0.7)->numerify('#########'),
                    'municipal_registration' => fake()->optional(0.5)->numerify('#########'),
                    'contributor_type' => fake()->randomElement([1, 2, 9]),
                    'is_active' => true,
                ]
            );
        }

        Client::factory()->count(5)->create([
            'user_id' => null,
            'is_active' => true,
        ]);
    }
}