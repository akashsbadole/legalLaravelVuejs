<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrustAccount extends Model
{
    protected $fillable = [
        'client_id', 'case_id', 'created_by', 'transaction_type',
        'amount', 'balance', 'description', 'reference_number',
        'transaction_date', 'status'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'transaction_date' => 'date',
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

    public static function getTransactionTypes(): array
    {
        return [
            'deposit' => 'Deposit',
            'withdrawal' => 'Withdrawal',
            'adjustment' => 'Adjustment',
            'transfer_to_operating' => 'Transfer to Operating',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    public function getIsDebitAttribute(): bool
    {
        return in_array($this->transaction_type, ['withdrawal', 'transfer_to_operating']);
    }
}

class TrustAccountActivity extends Model
{
    protected $table = 'trust_account_activities';

    protected $fillable = [
        'trust_account_id', 'user_id', 'action', 'amount_before',
        'amount_after', 'description'
    ];

    protected function casts(): array
    {
        return [
            'amount_before' => 'decimal:2',
            'amount_after' => 'decimal:2',
        ];
    }

    public function trustAccount(): BelongsTo
    {
        return $this->belongsTo(TrustAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
