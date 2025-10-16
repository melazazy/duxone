<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $type
 * @property float $balance
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Account|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Account> $children
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JournalLine> $journalLines
 */
class Account extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\Accounting\AccountFactory::new();
    }

    protected $fillable = [
        'name',
        'code',
        'type',
        'balance',
        'parent_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The possible account types.
     *
     * @var array
     */
    public const TYPES = [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'expense' => 'Expense',
    ];

    /**
     * Get the parent account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get the child accounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get the journal lines for this account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    /**
     * Get all descendants of this account (recursive).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDescendants()
    {
        return $this->children()->with('getAllDescendants')->get();
    }

    /**
     * Get the full path of the account (e.g., "1 > 11 > 111").
     *
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->code . ' - ' . $this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->code . ' - ' . $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get the account level in the hierarchy.
     *
     * @return int
     */
    public function getLevelAttribute(): int
    {
        $level = 0;
        $parent = $this->parent;

        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }

        return $level;
    }

    /**
     * Check if this account is a leaf node (no children).
     *
     * @return bool
     */
    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    /**
     * Check if this account is a root node (no parent).
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Scope a query to only include accounts of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include root accounts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include leaf accounts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeaf($query)
    {
        return $query->whereNotIn('id', function ($query) {
            $query->select('parent_id')->from('accounts')->whereNotNull('parent_id');
        });
    }
}
