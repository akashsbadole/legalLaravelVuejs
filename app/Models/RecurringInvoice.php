<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringInvoice extends Model
{
    protected $table = 'recurring_invoices';
    
    protected $fillable = [
        'client_id', 'case_id', 'created_by', 'description', 'amount',
        'frequency', 'start_date', 'end_date', 'next_invoice_date',
        'line_items_json', 'status', 'auto_send'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'next_invoice_date' => 'date',
            'auto_send' => 'boolean',
            'line_items_json' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getFrequencies(): array
    {
        return [
            'weekly' => 'Weekly',
            'biweekly' => 'Bi-weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'paused' => 'Paused',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
        ];
    }

    public function calculateNextInvoiceDate(): \Carbon\Carbon
    {
        $date = \Carbon\Carbon::parse($this->next_invoice_date);
        
        switch ($this->frequency) {
            case 'weekly':
                return $date->addWeek();
            case 'biweekly':
                return $date->addWeeks(2);
            case 'monthly':
                return $date->addMonth();
            case 'quarterly':
                return $date->addMonths(3);
            case 'yearly':
                return $date->addYear();
            default:
                return $date->addMonth();
        }
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               (!$this->end_date || now()->lte($this->end_date));
    }
}
