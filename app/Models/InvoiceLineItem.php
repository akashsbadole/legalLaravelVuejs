<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLineItem extends Model
{
    protected $fillable = ['invoice_id', 'description', 'type', 'reference_id', 'quantity', 'unit_price', 'amount'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function reference()
    {
        if ($this->type === 'time') {
            return $this->belongsTo(TimeEntry::class, 'reference_id');
        } elseif ($this->type === 'expense') {
            return $this->belongsTo(Expense::class, 'reference_id');
        }

        return null;
    }
}
