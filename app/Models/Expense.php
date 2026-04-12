<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ['case_id', 'category_id', 'user_id', 'description', 'amount', 'expense_date', 'is_billable', 'status'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_billable' => 'boolean',
            'expense_date' => 'date',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}