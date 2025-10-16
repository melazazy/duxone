<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\JournalLine>
 */
class JournalLineFactory extends Factory
{
    protected $model = JournalLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 1000);
        $isDebit = $this->faker->boolean();

        return [
            'journal_entry_id' => JournalEntry::factory(),
            'account_id' => Account::factory(),
            'debit' => $isDebit ? $amount : null,
            'credit' => $isDebit ? null : $amount,
        ];
    }

    /**
     * Indicate that the journal line is a debit.
     *
     * @param  float|null  $amount
     * @return static
     */
    public function debit(?float $amount = null): static
    {
        return $this->state(fn (array $attributes) => [
            'debit' => $amount ?? $this->faker->randomFloat(2, 10, 1000),
            'credit' => null,
        ]);
    }

    /**
     * Indicate that the journal line is a credit.
     *
     * @param  float|null  $amount
     * @return static
     */
    public function credit(?float $amount = null): static
    {
        return $this->state(fn (array $attributes) => [
            'debit' => null,
            'credit' => $amount ?? $this->faker->randomFloat(2, 10, 1000),
        ]);
    }

    /**
     * Indicate that the journal line is for a specific account.
     *
     * @param  Account  $account
     * @return static
     */
    public function forAccount(Account $account): static
    {
        return $this->state(fn (array $attributes) => [
            'account_id' => $account->id,
        ]);
    }

    /**
     * Indicate that the journal line is for a specific journal entry.
     *
     * @param  JournalEntry  $journalEntry
     * @return static
     */
    public function forJournalEntry(JournalEntry $journalEntry): static
    {
        return $this->state(fn (array $attributes) => [
            'journal_entry_id' => $journalEntry->id,
        ]);
    }

    /**
     * Indicate that the journal line has a specific amount.
     *
     * @param  float  $amount
     * @return static
     */
    public function withAmount(float $amount): static
    {
        return $this->state(function (array $attributes) use ($amount) {
            $isDebit = $attributes['debit'] !== null;
            
            return [
                'debit' => $isDebit ? $amount : null,
                'credit' => $isDebit ? null : $amount,
            ];
        });
    }

    /**
     * Create a balanced pair of journal lines (debit and credit).
     *
     * @param  JournalEntry  $journalEntry
     * @param  Account  $debitAccount
     * @param  Account  $creditAccount
     * @param  float  $amount
     * @return array
     */
    public function createBalancedPair(JournalEntry $journalEntry, Account $debitAccount, Account $creditAccount, float $amount): array
    {
        $debitLine = JournalLine::factory()
            ->forJournalEntry($journalEntry)
            ->forAccount($debitAccount)
            ->debit($amount)
            ->create();

        $creditLine = JournalLine::factory()
            ->forJournalEntry($journalEntry)
            ->forAccount($creditAccount)
            ->credit($amount)
            ->create();

        return [$debitLine, $creditLine];
    }
}
