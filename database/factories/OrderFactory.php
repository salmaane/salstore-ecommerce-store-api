<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'order_date' => fake()->dateTimeBetween('-2 months'),
            'status' => fake()->randomElement(['delivered', 'pending', 'shipped']),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            $orderItems = OrderItem::factory(rand(1 ,4))->create([
                'order_id' => $order->id,
            ]);

            $total_amount = $orderItems->sum('price');

            $order->update(['total_amount' => $total_amount]);
        });
    }
}
