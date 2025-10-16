<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string|null $description
 * @property string $reference_type
 * @property int $reference_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model|\Eloquent $reference
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JournalLine> $lines
 */
class JournalEntry extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\Accounting\JournalEntryFactory::new();
    }

    protected $fillable = [
        'transaction_date',
        'description',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent reference model (invoice, payment, etc.).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the journal lines for this entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    /**
     * Check if the journal entry is balanced (debits = credits).
     *
     * @return bool
     */
    public function isBalanced(): bool
    {
        $totalDebits = $this->lines()->sum('debit');
        $totalCredits = $this->lines()->sum('credit');

        return abs($totalDebits - $totalCredits) < 0.01; // Allow for floating point precision
    }

    /**
     * Get the total debit amount.
     *
     * @return float
     */
    public function getTotalDebitAttribute(): float
    {
        return $this->lines()->sum('debit');
    }

    /**
     * Get the total credit amount.
     *
     * @return float
     */
    public function getTotalCreditAttribute(): float
    {
        return $this->lines()->sum('credit');
    }

    /**
     * Create journal entry with lines.
     *
     * @param array $data
     * @param array $lines
     * @return static
     */
    public static function createWithLines(array $data, array $lines): static
    {
        $entry = static::create($data);

        foreach ($lines as $line) {
            $entry->lines()->create($line);
        }

        return $entry;
    }

    /**
     * Scope a query to only include entries within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include entries for a specific reference type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForReference($query, $type)
    {
        return $query->where('reference_type', $type);
    }
}
