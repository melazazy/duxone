<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Invoice;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'description' => $this->faker->sentence(),
            'reference_type' => $this->faker->randomElement([Invoice::class, Payment::class]),
            'reference_id' => function (array $attributes) {
                return match($attributes['reference_type']) {
                    Invoice::class => Invoice::factory(),
                    Payment::class => Payment::factory(),
                    default => Invoice::factory(),
                };
            },
        ];
    }

    /**
     * Indicate that the journal entry is for an invoice.
     *
     * @param  Invoice|null  $invoice
     * @return static
     */
    public function forInvoice(?Invoice $invoice = null): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_type' => Invoice::class,
            'reference_id' => $invoice?->id ?? Invoice::factory(),
            'description' => 'Invoice #' . ($invoice?->invoice_number ?? 'generated'),
        ]);
    }

    /**
     * Indicate that the journal entry is for a payment.
     *
     * @param  Payment|null  $payment
     * @return static
     */
    public function forPayment(?Payment $payment = null): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_type' => Payment::class,
            'reference_id' => $payment?->id ?? Payment::factory(),
            'description' => 'Payment received',
        ]);
    }

    /**
     * Indicate that the journal entry has a specific description.
     *
     * @param  string  $description
     * @return static
     */
    public function withDescription(string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $description,
        ]);
    }

    /**
     * Indicate that the journal entry is for a specific date.
     *
     * @param  string  $date
     * @return static
     */
    public function onDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_date' => $date,
        ]);
    }
}
