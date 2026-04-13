<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'aadhaar_number',
        'pan_number',
        'gstin',
        'client_type',
        'company_name',
        'designation',
        'kyc_status',
        'kyc_verified_at',
        'kyc_verified_by',
        'priority',
        'client_segment',
        'notes',
        'email_notifications',
        'sms_notifications',
        'whatsapp_notifications',
        'marketing_consent',
        'data_processing_consent',
        'consent_updated_at',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'kyc_verified_at' => 'datetime',
        'consent_updated_at' => 'datetime',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'whatsapp_notifications' => 'boolean',
        'marketing_consent' => 'boolean',
        'data_processing_consent' => 'boolean',
        'kyc_verified_by' => 'integer',
        'aadhaar_number' => 'encrypted',
        'pan_number' => 'encrypted',
        'gstin' => 'encrypted',
        'phone' => 'encrypted',
        'alternate_phone' => 'encrypted',
        'address_line1' => 'encrypted',
        'address_line2' => 'encrypted',
    ];

    // Relationships
    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(LegalCase::class, 'case_clients', 'client_id', 'case_id')
            ->withPivot('role', 'notes')
            ->withTimestamps();
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'owner');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function trustAccounts(): HasMany
    {
        return $this->hasMany(TrustAccount::class, 'client_id');
    }

    public function timeEntries(): HasManyThrough
    {
        return $this->hasManyThrough(TimeEntry::class, LegalCase::class);
    }

    public function expenses(): HasManyThrough
    {
        return $this->hasManyThrough(Expense::class, LegalCase::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function getUserAttribute(): ?User
    {
        return User::where('email', $this->email)->first();
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->diffInYears(now());
    }

    public function getIsKycVerifiedAttribute()
    {
        return $this->kyc_status === 'verified';
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->invoices()
            ->whereIn('status', ['sent', 'viewed', 'partial'])
            ->sum(fn($invoice) => $invoice->balance_due);
    }

    public function getTotalInvoicedAmountAttribute()
    {
        return $this->invoices()->sum('total_amount');
    }

    public function getTotalPaidAmountAttribute()
    {
        return $this->invoices()->sum('amount_paid');
    }

    public function getLtvAttribute()
    {
        return $this->getTotalPaidAmountAttribute();
    }

    // Scope methods for filtering
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeIndividual($query)
    {
        return $query->where('client_type', 'individual');
    }

    public function scopeCorporate($query)
    {
        return $query->where('client_type', 'corporate');
    }

    public function scopeWithKycVerified($query)
    {
        return $query->where('kyc_status', 'verified');
    }

    public function scopeHighValue($query)
    {
        return $query->where('priority', 'high');
    }
}
