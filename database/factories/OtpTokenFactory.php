<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Turahe\Otp\Models\OtpToken;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Turahe\Otp\Models\OtpToken>
 */
class OtpTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OtpToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identity' => $this->faker->email(),
            'token' => $this->faker->numerify('######'), // 6-digit numeric token
            'expired' => Carbon::now()->addMinutes(15), // Default 15 minutes expiry
        ];
    }

    /**
     * Indicate that the token is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expired' => Carbon::now()->subMinutes(rand(1, 60)),
        ]);
    }

    /**
     * Indicate that the token is valid (not expired).
     */
    public function valid(): static
    {
        return $this->state(fn (array $attributes) => [
            'expired' => Carbon::now()->addMinutes(rand(1, 60)),
        ]);
    }

    /**
     * Set the identity to an email address.
     */
    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'identity' => $this->faker->email(),
        ]);
    }

    /**
     * Set the identity to a phone number.
     */
    public function phone(): static
    {
        return $this->state(fn (array $attributes) => [
            'identity' => $this->faker->numerify('+62##########'), // Indonesian phone format
        ]);
    }

    /**
     * Set a specific expiry time.
     */
    public function expiresAt(Carbon $expiryTime): static
    {
        return $this->state(fn (array $attributes) => [
            'expired' => $expiryTime,
        ]);
    }

    /**
     * Set a specific token value.
     */
    public function withToken(string $token): static
    {
        return $this->state(fn (array $attributes) => [
            'token' => $token,
        ]);
    }

    /**
     * Set a specific identity.
     */
    public function withIdentity(string $identity): static
    {
        return $this->state(fn (array $attributes) => [
            'identity' => $identity,
        ]);
    }
} 