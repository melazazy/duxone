<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $invoice_id
 * @property string $description
 * @property float $quantity
 * @property float $unit_price
 * @property float $total
 * @property int $item_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Invoice $invoice
 */
class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\Accounting\InvoiceItemFactory::new();
    }

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total',
        'item_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the invoice that owns the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate total amount for the item.
     *
     * @param float $quantity
     * @param float $unitPrice
     * @return float
     */
    public static function calculateTotal(float $quantity, float $unitPrice): float
    {
        return $quantity * $unitPrice;
    }

    /**
     * Get the item that owns the invoice item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @property int $item_id
     * @property Item $item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if ($item->isDirty('quantity') || $item->isDirty('unit_price')) {
                $item->total = self::calculateTotal($item->quantity, $item->unit_price);
            }
        });
    }
}
