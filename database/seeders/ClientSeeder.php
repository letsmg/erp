<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = 'Mudar@123';

        // Criar 10 usuários clientes com padrão cli@1.com, cli@2.com...
        for ($i = 1; $i <= 10; $i++) {
            $email = "cli@$i.com";

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Cliente Teste $i",
                    'email' => $email,
                    'password' => $password,
                    'access_level' => 2, // CLIENT
                    'is_active' => true,
                ]
            );

            // Garante que o cliente associado ao usuário também exista
            Client::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => "Cliente Teste $i",
                    'document_type' => fake()->randomElement(['CPF', 'CNPJ']),
                    'document_number' => fake()->numerify(
                        fake()->randomElement(['###########', '##############'])
                    ),
                    'contact1'=> fake()->sentence(),
                    'phone1' => fake()->phoneNumber(), // ou fake()->numerify('119########')
                    'state_registration' => fake()->optional(0.7)->numerify('#########'),
                    'municipal_registration' => fake()->optional(0.5)->numerify('#########'),
                    'contributor_type' => fake()->randomElement([1, 2, 9]),
                    'is_active' => true,
                ]
            );
        }

        // Criar alguns clientes sem usuário (para testes de prospecção/vendas diretas)
        Client::factory()->count(5)->create([
            'user_id' => null,
            'is_active' => true,
        ]);
    }
}
