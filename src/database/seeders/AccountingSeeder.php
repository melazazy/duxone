<?php

namespace Database\Seeders;

use App\Models\Accounting\Account;
use App\Models\Accounting\Client;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\InvoiceItem;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalLine;
use App\Models\Accounting\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Check if accounts table exists
        if (!Schema::hasTable('accounts')) {
            $this->command->error('Accounts table does not exist. Please run the accounts migration first.');
            return;
        }

        // Check if required tables exist
        $requiredTables = ['clients', 'invoices', 'invoice_items', 'payments', 'journal_entries', 'journal_lines'];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->command->error("Required table '{$table}' does not exist.");
                return;
            }
        }

        DB::beginTransaction();
        try {
            // Create a user for created_by fields
            $user = User::factory()->create([
                'name' => 'System User',
                'email' => 'system@example.com',
            ]);

            // Create standard chart of accounts
            $this->createChartOfAccounts();

            // Create sample clients
            $clients = Client::factory()
                ->count(5)
                ->create(['created_by' => $user->id]);

            // Create sample invoices with items and payments
            foreach ($clients as $client) {
                $this->createClientInvoices($client, $user);
            }

            DB::commit();
            $this->command->info('Accounting data seeded successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to seed accounting data: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a standard chart of accounts.
     *
     * @return void
     */
    private function createChartOfAccounts(): void
    {
        // Check if accounts already exist to avoid duplicates
        if (Account::count() > 0) {
            $this->command->info('Chart of accounts already exists. Skipping creation.');
            return;
        }

        // Create root accounts
        $assets = Account::create([
            'name' => 'Assets',
            'code' => '1',
            'type' => 'asset',
            'balance' => 0,
        ]);

        $liabilities = Account::create([
            'name' => 'Liabilities',
            'code' => '2',
            'type' => 'liability',
            'balance' => 0,
        ]);

        $equity = Account::create([
            'name' => 'Equity',
            'code' => '3',
            'type' => 'equity',
            'balance' => 0,
        ]);

        $revenue = Account::create([
            'name' => 'Revenue',
            'code' => '4',
            'type' => 'revenue',
            'balance' => 0,
        ]);

        $expenses = Account::create([
            'name' => 'Expenses',
            'code' => '5',
            'type' => 'expense',
            'balance' => 0,
        ]);

        // Create asset sub-accounts
        $cash = Account::create([
            'name' => 'Cash',
            'code' => '1001',
            'type' => 'asset',
            'balance' => 50000,
            'parent_id' => $assets->id,
        ]);

        $accountsReceivable = Account::create([
            'name' => 'Accounts Receivable',
            'code' => '1002',
            'type' => 'asset',
            'balance' => 0,
            'parent_id' => $assets->id,
        ]);

        // Create liability sub-accounts
        $accountsPayable = Account::create([
            'name' => 'Accounts Payable',
            'code' => '2001',
            'type' => 'liability',
            'balance' => 0,
            'parent_id' => $liabilities->id,
        ]);

        $vatPayable = Account::create([
            'name' => 'VAT Payable',
            'code' => '2002',
            'type' => 'liability',
            'balance' => 0,
            'parent_id' => $liabilities->id,
        ]);

        // Create equity sub-accounts
        $ownerEquity = Account::create([
            'name' => 'Owner Equity',
            'code' => '3001',
            'type' => 'equity',
            'balance' => 50000,
            'parent_id' => $equity->id,
        ]);

        // Create revenue sub-accounts
        $salesRevenue = Account::create([
            'name' => 'Sales Revenue',
            'code' => '4001',
            'type' => 'revenue',
            'balance' => 0,
            'parent_id' => $revenue->id,
        ]);

        $vatRevenue = Account::create([
            'name' => 'VAT Revenue',
            'code' => '4002',
            'type' => 'revenue',
            'balance' => 0,
            'parent_id' => $revenue->id,
        ]);

        // Create expense sub-accounts
        $operatingExpenses = Account::create([
            'name' => 'Operating Expenses',
            'code' => '5001',
            'type' => 'expense',
            'balance' => 0,
            'parent_id' => $expenses->id,
        ]);

        $this->command->info('Chart of accounts created successfully!');
    }

    /**
     * Create invoices for a client.
     *
     * @param  Client  $client
     * @param  User  $user
     * @return void
     */
    private function createClientInvoices(Client $client, User $user): void
    {
        // Create 2-4 invoices per client
        $invoiceCount = rand(2, 4);
        
        for ($i = 0; $i < $invoiceCount; $i++) {
            $invoice = Invoice::factory()->create([
                'client_id' => $client->id,
                'created_by' => $user->id,
            ]);

            // Create journal entry for the invoice
            $this->createInvoiceJournalEntry($invoice);

            // Randomly create payments for some invoices
            if (rand(0, 1) || $invoice->status === 'paid') {
                $this->createInvoicePayments($invoice, $user);
            }
        }
    }

    /**
     * Create journal entry for an invoice.
     *
     * @param  Invoice  $invoice
     * @return void
     */
    private function createInvoiceJournalEntry(Invoice $invoice): void
    {
        // Get accounts
        $accountsReceivable = Account::where('code', '1002')->first();
        $salesRevenue = Account::where('code', '4001')->first();
        $vatRevenue = Account::where('code', '4002')->first();

        if (!$accountsReceivable || !$salesRevenue || !$vatRevenue) {
            $this->command->error('Required accounts not found for journal entry!');
            return;
        }

        $journalEntry = JournalEntry::create([
            'transaction_date' => $invoice->invoice_date,
            'description' => "Invoice #{$invoice->invoice_number} - {$invoice->client->name}",
            'reference_type' => Invoice::class,
            'reference_id' => $invoice->id,
        ]);

        // Create journal lines
        // Debit Accounts Receivable for total amount
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $accountsReceivable->id,
            'debit' => $invoice->total,
            'credit' => null,
        ]);

        // Credit Sales Revenue for subtotal
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $salesRevenue->id,
            'debit' => null,
            'credit' => $invoice->subtotal,
        ]);

        // Credit VAT Revenue for VAT amount
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $vatRevenue->id,
            'debit' => null,
            'credit' => $invoice->vat_amount,
        ]);

        // Update account balances
        $accountsReceivable->increment('balance', $invoice->total);
        $salesRevenue->increment('balance', $invoice->subtotal);
        $vatRevenue->increment('balance', $invoice->vat_amount);
    }

    /**
     * Create payments for an invoice.
     *
     * @param  Invoice  $invoice
     * @param  User  $user
     * @return void
     */
    private function createInvoicePayments(Invoice $invoice, User $user): void
    {
        // Determine payment amount (could be partial or full)
        $paymentAmount = $invoice->status === 'paid' 
            ? $invoice->total 
            : $invoice->total * rand(50, 90) / 100;

        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => $paymentAmount,
            'status' => 'confirmed',
            'created_by' => $user->id,
        ]);

        // Create journal entry for the payment
        $this->createPaymentJournalEntry($payment);

        // Update invoice status if fully paid
        $totalPaid = $invoice->payments()->where('status', 'confirmed')->sum('amount');
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        }
    }

    /**
     * Create journal entry for a payment.
     *
     * @param  Payment  $payment
     * @return void
     */
    private function createPaymentJournalEntry(Payment $payment): void
    {
        // Get accounts
        $cash = Account::where('code', '1001')->first();
        $accountsReceivable = Account::where('code', '1002')->first();

        if (!$cash || !$accountsReceivable) {
            $this->command->error('Required accounts not found for payment journal entry!');
            return;
        }

        $journalEntry = JournalEntry::create([
            'transaction_date' => $payment->payment_date,
            'description' => "Payment received for Invoice #{$payment->invoice->invoice_number}",
            'reference_type' => Payment::class,
            'reference_id' => $payment->id,
        ]);

        // Create journal lines
        // Debit Cash for payment amount
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $cash->id,
            'debit' => $payment->amount,
            'credit' => null,
        ]);

        // Credit Accounts Receivable for payment amount
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $accountsReceivable->id,
            'debit' => null,
            'credit' => $payment->amount,
        ]);

        // Update account balances
        $cash->increment('balance', $payment->amount);
        $accountsReceivable->decrement('balance', $payment->amount);
    }
}
