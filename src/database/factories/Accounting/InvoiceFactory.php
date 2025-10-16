<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Client;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoiceDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = $this->faker->dateTimeBetween($invoiceDate, '+30 days');
        
        return [
            'client_id' => Client::factory(),
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('########'),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'status' => $this->faker->randomElement(['draft', 'sent', 'paid', 'overdue']),
            'subtotal' => 0, // Will be calculated after items are created
            'vat_amount' => 0, // Will be calculated after items are created
            'total' => 0, // Will be calculated after items are created
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Invoice $invoice) {
            // Create 2-4 invoice items
            $items = InvoiceItem::factory()
                ->count($this->faker->numberBetween(2, 4))
                ->create(['invoice_id' => $invoice->id]);

            // Calculate totals
            $subtotal = $items->sum('total');
            $vatRate = 0.15; // 15% VAT
            $vatAmount = $subtotal * $vatRate;
            $total = $subtotal + $vatAmount;

            // Update invoice with calculated totals
            $invoice->update([
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total' => $total,
            ]);
        });
    }

    /**
     * Indicate that the invoice is in draft status.
     *
     * @return static
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the invoice is in sent status.
     *
     * @return static
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    /**
     * Indicate that the invoice is paid.
     *
     * @return static
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Indicate that the invoice is overdue.
     *
     * @return static
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }
}
