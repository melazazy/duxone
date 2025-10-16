<?php

namespace App\Modules\Accounting\Repositories\Eloquent;

use App\Models\Accounting\Invoice;
use App\Models\Accounting\Client;
use App\Models\Accounting\InvoiceItem;
use App\Modules\Accounting\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Eloquent implementation of Invoice Repository
 * 
 * Handles invoice data operations using Eloquent ORM with optimized queries,
 * eager loading, and proper filtering logic.
 */
class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * Get paginated list of invoices with optional filters
     *
     * @param array $filters Filters to apply (e.g., status, client_id, date_range)
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Invoice::with(['client', 'items', 'creator'])
            ->when(isset($filters['status']), function ($q) use ($filters) {
                return $q->where('status', $filters['status']);
            })
            ->when(isset($filters['client_id']), function ($q) use ($filters) {
                return $q->where('client_id', $filters['client_id']);
            })
            ->when(isset($filters['date_from']), function ($q) use ($filters) {
                return $q->where('invoice_date', '>=', Carbon::parse($filters['date_from']));
            })
            ->when(isset($filters['date_to']), function ($q) use ($filters) {
                return $q->where('invoice_date', '<=', Carbon::parse($filters['date_to']));
            })
            ->when(isset($filters['min_amount']), function ($q) use ($filters) {
                return $q->where('total', '>=', $filters['min_amount']);
            })
            ->when(isset($filters['max_amount']), function ($q) use ($filters) {
                return $q->where('total', '<=', $filters['max_amount']);
            })
            ->latest();

        return $query->paginate($perPage);
    }

    /**
     * Find an invoice by its ID
     *
     * @param int $id Invoice ID
     * @return Invoice|null
     */
    public function find(int $id): ?Invoice
    {
        return Invoice::with(['client', 'items', 'creator', 'payments'])
            ->find($id);
    }

    /**
     * Find an invoice by its invoice number
     *
     * @param string $invoiceNumber Unique invoice number
     * @return Invoice|null
     */
    public function findByNumber(string $invoiceNumber): ?Invoice
    {
        return Invoice::with(['client', 'items', 'creator', 'payments'])
            ->where('invoice_number', $invoiceNumber)
            ->first();
    }

    /**
     * Get invoices for a specific client within a date range
     *
     * @param int $clientId Client ID
     * @param Carbon $from Start date
     * @param Carbon $to End date
     * @return Collection
     */
    public function getByClientAndDateRange(int $clientId, Carbon $from, Carbon $to): Collection
    {
        return Invoice::with(['items', 'payments'])
            ->where('client_id', $clientId)
            ->whereBetween('invoice_date', [$from, $to])
            ->latest()
            ->get();
    }

    /**
     * Search invoices by keyword across relevant fields
     *
     * @param string $keyword Search term
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::with(['client', 'items', 'creator'])
            ->where(function ($query) use ($keyword) {
                $query->where('invoice_number', 'like', "%{$keyword}%")
                    ->orWhere('notes', 'like', "%{$keyword}%")
                    ->orWhereHas('client', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('items', function ($q) use ($keyword) {
                        $q->where('description', 'like', "%{$keyword}%");
                    });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new invoice with associated items
     *
     * @param array $data Invoice data including items
     * @return Invoice
     */
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Extract items from data if present
            $items = $data['items'] ?? [];
            unset($data['items']);

            // Create invoice
            $invoice = Invoice::create($data);

            // Create invoice items if any
            if (!empty($items)) {
                $invoiceItems = [];
                foreach ($items as $item) {
                    $invoiceItems[] = new InvoiceItem($item);
                }
                $invoice->items()->saveMany($invoiceItems);
            }

            // Reload relationships
            $invoice->load(['client', 'items', 'creator']);

            return $invoice;
        });
    }

    /**
     * Update an existing invoice
     *
     * @param int $id Invoice ID
     * @param array $data Updated invoice data
     * @return Invoice|null
     */
    public function update(int $id, array $data): ?Invoice
    {
        return DB::transaction(function () use ($id, $data) {
            $invoice = $this->find($id);
            
            if (!$invoice) {
                return null;
            }

            // Extract items from data if present
            $items = $data['items'] ?? null;
            unset($data['items']);

            // Update invoice
            $invoice->update($data);

            // Update items if provided
            if ($items !== null) {
                // Remove existing items
                $invoice->items()->delete();
                
                // Add new items
                if (!empty($items)) {
                    $invoiceItems = [];
                    foreach ($items as $item) {
                        $invoiceItems[] = new InvoiceItem($item);
                    }
                    $invoice->items()->saveMany($invoiceItems);
                }
            }

            // Reload relationships
            $invoice->load(['client', 'items', 'creator', 'payments']);

            return $invoice;
        });
    }

    /**
     * Soft delete an invoice
     *
     * @param int $id Invoice ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool
    {
        $invoice = $this->find($id);
        
        if (!$invoice) {
            return false;
        }

        return $invoice->delete();
    }
}
