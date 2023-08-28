<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialLink>
 */
class SocialLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'instagram' => 'www.instagram.com',
            'facebook' => 'www.facebook.com',
            'twitter' => 'www.twitter.com',
            'linkedin' => 'www.linkedin.com',
        ];
    }
}
