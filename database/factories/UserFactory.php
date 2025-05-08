<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        
        return [
            'username' => strtolower($firstName . '.' . $lastName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => static::$password ??= Hash::make('password'),
            'phone_number' => fake()->phoneNumber(),
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model should have tenant role.
     */
    public function tenant(): static
    {
        return $this->afterCreating(function ($user) {
            $user->roles()->attach(1); // Assuming tenant role has ID 1
        });
    }
    
    /**
     * Indicate that the model should have landlord role.
     */
    public function landlord(): static
    {
        return $this->afterCreating(function ($user) {
            $user->roles()->attach(2); // Assuming landlord role has ID 2
        });
    }
}
