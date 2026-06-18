<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $displayName = "{$firstName} {$lastName}";
        $email = fake()->unique()->safeEmail();

        return [
            'password' => static::$password ??= Hash::make('Mudar@123'),
            'first_name_hash' => hash('sha256', $firstName),
            'first_name_encrypted' => Crypt::encryptString($firstName),
            'last_name_hash' => hash('sha256', $lastName),
            'last_name_encrypted' => Crypt::encryptString($lastName),
            'display_name' => $displayName,
            'email_hash' => hash('sha256', $email),
            'email_encrypted' => Crypt::encryptString($email),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'access_level' => 0,
            'is_active' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => 1,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => 2,
        ]);
    }
}