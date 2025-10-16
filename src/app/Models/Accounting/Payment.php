<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $invoice_id
 * @property \Illuminate\Support\Carbon $payment_date
 * @property float $amount
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $transaction_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Invoice $invoice
 * @property-read User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JournalEntry> $journalEntries
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\Accounting\PaymentFactory::new();
    }

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The possible payment statuses.
     *
     * @var array
     */
    public const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'failed' => 'Failed',
    ];

    /**
     * Get the invoice that owns the payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user who created this payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the journal entries for this payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }

    /**
     * Scope a query to only include confirmed payments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include pending payments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark the payment as confirmed.
     *
     * @return bool
     */
    public function markAsConfirmed(): bool
    {
        return $this->update(['status' => 'confirmed']);
    }

    /**
     * Mark the payment as failed.
     *
     * @return bool
     */
    public function markAsFailed(): bool
    {
        return $this->update(['status' => 'failed']);
    }
}
