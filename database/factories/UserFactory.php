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
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dob' => fake()->date(),
            'phone_number' => fake()->phoneNumber(),
          //  'user_role' => 'PUT_A_VALID_ROLE_UUID_HERE', // required FK
           // 'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
