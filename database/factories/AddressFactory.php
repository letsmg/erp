<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $street = fake()->streetName();
        $neighborhood = fake()->word();
        $city = fake()->city();
        $zipCode = fake()->numerify('#####-###');

        return [
            'client_id' => Client::factory(),
            'zip_code' => $zipCode,
            'zip_code_hash' => hash('sha256', $zipCode),
            'zip_code_encrypted' => Crypt::encryptString($zipCode),
            'street' => $street,
            'street_hash' => hash('sha256', $street),
            'street_encrypted' => Crypt::encryptString($street),
            'number' => fake()->buildingNumber(),
            'neighborhood' => $neighborhood,
            'neighborhood_hash' => hash('sha256', $neighborhood),
            'neighborhood_encrypted' => Crypt::encryptString($neighborhood),
            'city' => $city,
            'city_hash' => hash('sha256', $city),
            'city_encrypted' => Crypt::encryptString($city),
            'state' => fake()->stateAbbr(),
            'complement' => fake()->optional(0.4)->secondaryAddress(),
            'is_delivery_address' => false,
        ];
    }

    public function deliveryAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_delivery_address' => true,
        ]);
    }

    public function commercial(): static
    {
        return $this->state(function (array $attributes) {
            $street = fake()->streetAddress();
            return [
                'street' => $street,
                'street_hash' => hash('sha256', $street),
                'street_encrypted' => Crypt::encryptString($street),
                'complement' => fake()->optional(0.6)->secondaryAddress(),
            ];
        });
    }

    public function residential(): static
    {
        return $this->state(function (array $attributes) {
            $street = fake()->streetName();
            return [
                'street' => $street,
                'street_hash' => hash('sha256', $street),
                'street_encrypted' => Crypt::encryptString($street),
                'complement' => fake()->optional(0.3)->secondaryAddress(),
            ];
        });
    }

    public function saoPaulo(): static
    {
        return $this->state(function (array $attributes) {
            $city = 'São Paulo';
            $zipCode = fake()->numerify('#####-###');
            return [
                'city' => $city,
                'city_hash' => hash('sha256', $city),
                'city_encrypted' => Crypt::encryptString($city),
                'state' => 'SP',
                'zip_code' => $zipCode,
                'zip_code_hash' => hash('sha256', $zipCode),
                'zip_code_encrypted' => Crypt::encryptString($zipCode),
            ];
        });
    }
}