<?php

namespace App\Models;

use App\Services\GSTService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'case_id', 'client_id', 'created_by', 'subtotal',
        'tax_amount', 'discount_amount', 'total_amount', 'status',
        'issue_date', 'due_date', 'paid_date', 'notes', 'gstin', 'sac_code',
        'cgst_amount', 'sgst_amount', 'igst_amount'
    ];

    protected $appends = ['cgst', 'sgst', 'igst'];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_date' => 'date',
            'gstin' => 'string',
            'sac_code' => 'string',
            'cgst_amount' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_amount' => 'decimal:2',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class, 'invoice_id');
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::where('invoice_number', 'like', "INV-{$year}{$month}%")->orderBy('invoice_number', 'desc')->first();
        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        return sprintf('INV-%s%s-%04d', $year, $month, $sequence);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'sent' && now()->gt($this->due_date);
    }

    /**
     * Calculate GST amounts based on subtotal
     */
    public function calculateGST()
    {
        $gstService = new GSTService();
        return $gstService->calculateGST($this->subtotal);
    }

    /**
     * Get CGST amount
     */
    public function getCgstAttribute()
    {
        return $this->cgst_amount ?? 0;
    }

    /**
     * Get SGST amount
     */
    public function getSgstAttribute()
    {
        return $this->sgst_amount ?? 0;
    }

    /**
     * Get IGST amount
     */
    public function getIgstAttribute()
    {
        return $this->igst_amount ?? 0;
    }
}