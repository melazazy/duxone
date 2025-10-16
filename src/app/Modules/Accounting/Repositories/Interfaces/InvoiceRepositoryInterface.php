<?php

namespace App\Modules\Accounting\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface for Invoice Repository
 * 
 * Defines the contract for invoice data operations, following the Repository Pattern
 * to separate data access logic from business logic.
 */
interface InvoiceRepositoryInterface
{
    /**
     * Get paginated list of invoices with optional filters
     *
     * @param array $filters Filters to apply (e.g., status, client_id, date_range)
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find an invoice by its ID
     *
     * @param int $id Invoice ID
     * @return \App\Models\Accounting\Invoice|null
     */
    public function find(int $id): ?\App\Models\Accounting\Invoice;

    /**
     * Find an invoice by its invoice number
     *
     * @param string $invoiceNumber Unique invoice number
     * @return \App\Models\Accounting\Invoice|null
     */
    public function findByNumber(string $invoiceNumber): ?\App\Models\Accounting\Invoice;

    /**
     * Get invoices for a specific client within a date range
     *
     * @param int $clientId Client ID
     * @param \Carbon\Carbon $from Start date
     * @param \Carbon\Carbon $to End date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByClientAndDateRange(int $clientId, \Carbon\Carbon $from, \Carbon\Carbon $to): \Illuminate\Database\Eloquent\Collection;

    /**
     * Search invoices by keyword across relevant fields
     *
     * @param string $keyword Search term
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a new invoice with associated items
     *
     * @param array $data Invoice data including items
     * @return \App\Models\Accounting\Invoice
     */
    public function create(array $data): \App\Models\Accounting\Invoice;

    /**
     * Update an existing invoice
     *
     * @param int $id Invoice ID
     * @param array $data Updated invoice data
     * @return \App\Models\Accounting\Invoice|null
     */
    public function update(int $id, array $data): ?\App\Models\Accounting\Invoice;

    /**
     * Soft delete an invoice
     *
     * @param int $id Invoice ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool;
}
