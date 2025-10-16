<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'code' => $this->faker->unique()->numerify('####'),
            'type' => $this->faker->randomElement(['asset', 'liability', 'equity', 'revenue', 'expense']),
            'balance' => $this->faker->randomFloat(2, -10000, 10000),
            'parent_id' => null,
        ];
    }

    /**
     * Indicate that the account is an asset.
     *
     * @return static
     */
    public function asset(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'asset',
        ]);
    }

    /**
     * Indicate that the account is a liability.
     *
     * @return static
     */
    public function liability(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'liability',
        ]);
    }

    /**
     * Indicate that the account is equity.
     *
     * @return static
     */
    public function equity(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'equity',
        ]);
    }

    /**
     * Indicate that the account is revenue.
     *
     * @return static
     */
    public function revenue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'revenue',
        ]);
    }

    /**
     * Indicate that the account is an expense.
     *
     * @return static
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
        ]);
    }

    /**
     * Indicate that the account has a parent.
     *
     * @param  int  $parentId
     * @return static
     */
    public function withParent(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Create a child account for the given parent.
     *
     * @param  Account  $parent
     * @param  int  $count
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createChildren(Account $parent, int $count = 3)
    {
        return $this->count($count)
            ->state(fn (array $attributes) => [
                'parent_id' => $parent->id,
                'type' => $parent->type, // Children inherit parent type
                'code' => function () use ($parent) {
                    $lastChild = $parent->children()->orderBy('code', 'desc')->first();
                    $lastCode = $lastChild ? (int) $lastChild->code : (int) $parent->code * 100;
                    return str_pad($lastCode + 1, 4, '0', STR_PAD_LEFT);
                },
            ])
            ->create();
    }

    /**
     * Create a standard chart of accounts structure.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createStandardChart()
    {
        $accounts = collect();

        // Create root accounts
        $assets = Account::factory()->asset()->create(['name' => 'Assets', 'code' => '1']);
        $liabilities = Account::factory()->liability()->create(['name' => 'Liabilities', 'code' => '2']);
        $equity = Account::factory()->equity()->create(['name' => 'Equity', 'code' => '3']);
        $revenue = Account::factory()->revenue()->create(['name' => 'Revenue', 'code' => '4']);
        $expenses = Account::factory()->expense()->create(['name' => 'Expenses', 'code' => '5']);

        $accounts->push($assets, $liabilities, $equity, $revenue, $expenses);

        // Create asset sub-accounts
        $cash = Account::factory()->asset()->withParent($assets->id)->create(['name' => 'Cash', 'code' => '1001']);
        $accountsReceivable = Account::factory()->asset()->withParent($assets->id)->create(['name' => 'Accounts Receivable', 'code' => '1002']);
        $accounts->push($cash, $accountsReceivable);

        // Create liability sub-accounts
        $accountsPayable = Account::factory()->liability()->withParent($liabilities->id)->create(['name' => 'Accounts Payable', 'code' => '2001']);
        $accounts->push($accountsPayable);

        // Create equity sub-accounts
        $ownerEquity = Account::factory()->equity()->withParent($equity->id)->create(['name' => 'Owner Equity', 'code' => '3001']);
        $accounts->push($ownerEquity);

        // Create revenue sub-accounts
        $salesRevenue = Account::factory()->revenue()->withParent($revenue->id)->create(['name' => 'Sales Revenue', 'code' => '4001']);
        $accounts->push($salesRevenue);

        // Create expense sub-accounts
        $operatingExpenses = Account::factory()->expense()->withParent($expenses->id)->create(['name' => 'Operating Expenses', 'code' => '5001']);
        $accounts->push($operatingExpenses);

        return $accounts;
    }
}
