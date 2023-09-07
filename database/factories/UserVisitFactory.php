<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserVisit>
 */
class UserVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomNumber = rand(1, 2);
        if ($randomNumber == 1) {
            $userId = User::all()->random()->id;
        } else {
            $userId = null;
        }

        return [
            'user_id' => $userId,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'visit_time' => fake()->dateTimeBetween('-2 months'),
            'country' => fake()->country(),
            'city' => fake()->city(),
        ];
    }

}
