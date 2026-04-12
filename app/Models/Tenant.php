<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'domain', 'logo', 'primary_color',
        'contact_email', 'contact_phone', 'address', 'GSTIN', 'PAN',
        'subscription_plan', 'subscription_status', 'subscription_expires_at',
        'max_users', 'max_cases', 'max_storage_mb', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'subscription_expires_at' => 'datetime',
            'is_active' => 'boolean',
            'max_storage_mb' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(\App\Models\LegalCase::class, 'tenant_id');
    }

    public static function getPlans(): array
    {
        return [
            'basic' => 'Basic',
            'professional' => 'Professional',
            'enterprise' => 'Enterprise',
        ];
    }

    public function isSubscriptionActive(): bool
    {
        return $this->subscription_status === 'active' 
            && (!$this->subscription_expires_at || $this->subscription_expires_at->isFuture());
    }
}
