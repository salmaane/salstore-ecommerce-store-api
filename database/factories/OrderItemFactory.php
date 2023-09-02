<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Sneaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sneaker = Sneaker::all()->random();
        $orderId = isset($attributes['order_id']) ? $attributes['order_id'] : Order::all()->random()->id;

        return [
            'sneaker_id' => $sneaker->id,
            'quantity' => 1,
            'price' => $sneaker->price,
        ];
    }


}
