<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $documentType = fake()->randomElement(['CPF', 'CNPJ']);
        $documentNumber = $documentType === 'CPF'
            ? $this->generateValidCPF()
            : $this->generateValidCNPJ();

        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $displayName = "{$firstName} {$lastName}";
        $email = fake()->unique()->companyEmail();
        $phone1 = fake()->phoneNumber();
        $phone2 = fake()->optional(0.6)->phoneNumber();

        return [
            'user_id' => null,
            'first_name_hash' => hash('sha256', $firstName),
            'first_name_encrypted' => Crypt::encryptString($firstName),
            'last_name_hash' => hash('sha256', $lastName),
            'last_name_encrypted' => Crypt::encryptString($lastName),
            'display_name' => $displayName,
            'email_hash' => hash('sha256', $email),
            'email_encrypted' => Crypt::encryptString($email),
            'name' => $displayName,
            'email' => $email,
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'document_hash' => hash('sha256', $documentNumber),
            'document_encrypted' => Crypt::encryptString($documentNumber),
            'phone1' => $phone1,
            'phone1_hash' => $phone1 ? hash('sha256', $phone1) : null,
            'phone1_encrypted' => $phone1 ? Crypt::encryptString($phone1) : null,
            'contact1' => fake()->name(),
            'phone2' => $phone2,
            'phone2_hash' => $phone2 ? hash('sha256', $phone2) : null,
            'phone2_encrypted' => $phone2 ? Crypt::encryptString($phone2) : null,
            'contact2' => fake()->optional(0.6)->name(),
            'state_registration' => fake()->optional(0.7)->numerify('#########'),
            'municipal_registration' => fake()->optional(0.5)->numerify('#########'),
            'contributor_type' => fake()->randomElement([1, 2, 9]),
            'is_active' => true,
        ];
    }

    private function generateValidCPF(): string
    {
        $cpf = [];
        for ($i = 0; $i < 9; $i++) {
            $cpf[] = fake()->randomDigit();
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            $cpf[] = $d;
        }

        return implode('', $cpf);
    }

    private function generateValidCNPJ(): string
    {
        $cnpj = [];
        for ($i = 0; $i < 12; $i++) {
            $cnpj[] = fake()->randomDigit();
        }

        $sum = 0;
        $weight = 5;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight === 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        $cnpj[] = $digit1;

        $sum = 0;
        $weight = 6;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight === 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        $cnpj[] = $digit2;

        return implode('', $cnpj);
    }

    public function individual(): static
    {
        return $this->state(function (array $attributes) {
            $doc = $this->generateValidCPF();
            return [
                'document_type' => 'CPF',
                'document_number' => $doc,
                'document_hash' => hash('sha256', $doc),
                'document_encrypted' => Crypt::encryptString($doc),
                'state_registration' => null,
                'municipal_registration' => null,
                'contributor_type' => fake()->randomElement([2, 9]),
            ];
        });
    }

    public function company(): static
    {
        return $this->state(function (array $attributes) {
            $doc = $this->generateValidCNPJ();
            return [
                'document_type' => 'CNPJ',
                'document_number' => $doc,
                'document_hash' => hash('sha256', $doc),
                'document_encrypted' => Crypt::encryptString($doc),
                'state_registration' => fake()->numerify('#########'),
                'municipal_registration' => fake()->numerify('#########'),
                'contributor_type' => fake()->randomElement([1, 2]),
            ];
        });
    }

    public function icmsContributor(): static
    {
        return $this->state(fn (array $attributes) => [
            'contributor_type' => 1,
            'state_registration' => fake()->numerify('#########'),
        ]);
    }

    public function icmsExempt(): static
    {
        return $this->state(fn (array $attributes) => [
            'contributor_type' => 2,
            'state_registration' => 'ISENTO',
        ]);
    }

    public function nonContributor(): static
    {
        return $this->state(fn (array $attributes) => [
            'contributor_type' => 9,
            'state_registration' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}