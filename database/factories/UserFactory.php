<?php

namespace Database\Factories;

use App\Models\User;
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
        return [
            'mobile' => '+989'.$this->faker->randomNumber(4) . $this->faker->randomNumber(5),
            'email'=>$this->faker->unique()->safeEmail,
            'name'=>$this->faker->name,
            'last_name'=>$this->faker->lastName,
            'username'=>$this->faker->userName,
            'password'=> '$2y$10$yHOVJzIYR5NRsj1JVFrKIuJ8X4JZHlW7Y7QAgRPpnd4MEp9uglwHK', // 123456
            'avatar'=> null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
