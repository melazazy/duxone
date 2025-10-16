<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $journal_entry_id
 * @property int $account_id
 * @property float|null $debit
 * @property float|null $credit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read JournalEntry $journalEntry
 * @property-read Account $account
 */
class JournalLine extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\Accounting\JournalLineFactory::new();
    }

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the journal entry that owns the line.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the account for this journal line.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the amount (debit or credit, whichever is not null).
     *
     * @return float|null
     */
    public function getAmountAttribute(): ?float
    {
        return $this->debit ?? $this->credit;
    }

    /**
     * Get the type of amount (debit or credit).
     *
     * @return string|null
     */
    public function getAmountTypeAttribute(): ?string
    {
        if ($this->debit !== null) {
            return 'debit';
        }
        
        if ($this->credit !== null) {
            return 'credit';
        }
        
        return null;
    }

    /**
     * Scope a query to only include debit lines.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDebits($query)
    {
        return $query->whereNotNull('debit');
    }

    /**
     * Scope a query to only include credit lines.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCredits($query)
    {
        return $query->whereNotNull('credit');
    }

    /**
     * Scope a query to only include lines for a specific account.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $accountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * Create a debit journal line.
     *
     * @param  int  $journalEntryId
     * @param  int  $accountId
     * @param  float  $amount
     * @return static
     */
    public static function createDebit(int $journalEntryId, int $accountId, float $amount): static
    {
        return static::create([
            'journal_entry_id' => $journalEntryId,
            'account_id' => $accountId,
            'debit' => $amount,
            'credit' => null,
        ]);
    }

    /**
     * Create a credit journal line.
     *
     * @param  int  $journalEntryId
     * @param  int  $accountId
     * @param  float  $amount
     * @return static
     */
    public static function createCredit(int $journalEntryId, int $accountId, float $amount): static
    {
        return static::create([
            'journal_entry_id' => $journalEntryId,
            'account_id' => $accountId,
            'debit' => null,
            'credit' => $amount,
        ]);
    }
}
