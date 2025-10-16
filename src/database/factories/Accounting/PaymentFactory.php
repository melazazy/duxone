<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Invoice;
use App\Models\Accounting\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'failed']),
            'payment_method' => $this->faker->randomElement(['Bank Transfer', 'Credit Card', 'Cash', 'Check']),
            'transaction_id' => $this->faker->optional()->numerify('TXN##########'),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the payment is pending.
     *
     * @return static
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the payment is confirmed.
     *
     * @return static
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the payment is failed.
     *
     * @return static
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Indicate that the payment is for a specific invoice amount.
     *
     * @param  float  $amount
     * @return static
     */
    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * Indicate that the payment is made by bank transfer.
     *
     * @return static
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'Bank Transfer',
        ]);
    }

    /**
     * Indicate that the payment is made by credit card.
     *
     * @return static
     */
    public function creditCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'Credit Card',
        ]);
    }
}
