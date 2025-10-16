<?php

namespace App\Modules\Accounting\Services;

use App\Modules\Accounting\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Models\Accounting\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * Invoice Service
 * 
 * Handles business logic for invoice operations, using the Repository Pattern
 * to separate data access concerns from business logic.
 */
class InvoiceService
{
    protected InvoiceRepositoryInterface $invoiceRepository;

    /**
     * InvoiceService constructor.
     *
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Get paginated list of invoices with optional filters
     *
     * @param array $filters Filters to apply (e.g., status, client_id, date_range)
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function listInvoices(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->invoiceRepository->paginate($filters, $perPage);
    }

    /**
     * Get detailed invoice information by ID
     *
     * @param int $id Invoice ID
     * @return Invoice|null
     */
    public function getInvoiceDetails(int $id): ?Invoice
    {
        return $this->invoiceRepository->find($id);
    }

    /**
     * Find an invoice by its invoice number
     *
     * @param string $invoiceNumber Unique invoice number
     * @return Invoice|null
     */
    public function getInvoiceByNumber(string $invoiceNumber): ?Invoice
    {
        return $this->invoiceRepository->findByNumber($invoiceNumber);
    }

    /**
     * Get invoices for a specific client within a date range
     *
     * @param int $clientId Client ID
     * @param Carbon $from Start date
     * @param Carbon $to End date
     * @return Collection
     */
    public function getClientInvoicesByDateRange(int $clientId, Carbon $from, Carbon $to): Collection
    {
        return $this->invoiceRepository->getByClientAndDateRange($clientId, $from, $to);
    }

    /**
     * Search invoices by keyword across relevant fields
     *
     * @param string $keyword Search term
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function searchInvoices(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->invoiceRepository->search($keyword, $perPage);
    }

    /**
     * Create a new invoice with business logic validation
     *
     * @param array $data Invoice data including items
     * @return Invoice
     */
    public function createInvoice(array $data): Invoice
    {
        // Add business logic here if needed
        // Example: Validate invoice number uniqueness, calculate totals, etc.
        
        return $this->invoiceRepository->create($data);
    }

    /**
     * Update an existing invoice with business logic validation
     *
     * @param int $id Invoice ID
     * @param array $data Updated invoice data
     * @return Invoice|null
     */
    public function updateInvoice(int $id, array $data): ?Invoice
    {
        // Add business logic here if needed
        // Example: Check if invoice can be updated (not paid, etc.)
        
        return $this->invoiceRepository->update($id, $data);
    }

    /**
     * Delete an invoice with business logic validation
     *
     * @param int $id Invoice ID
     * @return bool True if successful, false otherwise
     */
    public function deleteInvoice(int $id): bool
    {
        // Add business logic here if needed
        // Example: Check if invoice can be deleted (no payments, etc.)
        
        return $this->invoiceRepository->delete($id);
    }

    /**
     * Get invoice statistics for a client
     *
     * @param int $clientId Client ID
     * @return array
     */
    public function getClientInvoiceStats(int $clientId): array
    {
        $invoices = $this->getClientInvoicesByDateRange(
            $clientId, 
            Carbon::now()->startOfYear(), 
            Carbon::now()->endOfYear()
        );

        return [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('total'),
            'paid_amount' => $invoices->sum('paid_amount'),
            'due_amount' => $invoices->sum('due_amount'),
            'overdue_invoices' => $invoices->where('due_date', '<', Carbon::now())
                ->where('status', '!=', 'paid')
                ->count(),
        ];
    }

    /**
     * Generate next invoice number
     *
     * @param int $clientId Client ID
     * @return string
     */
    public function generateInvoiceNumber(int $clientId): string
    {
        // This is a simple example - implement your own numbering logic
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $lastInvoice = Invoice::where('client_id', $clientId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->first();

        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return sprintf('INV-%s-%s-%04d', $clientId, $year . $month, $sequence);
    }
}
