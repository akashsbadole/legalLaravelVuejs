<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, BelongsToTenant;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'address', 'hourly_rate', 'is_active', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_enabled', 'tenant_id', 'communication_preferences', 'communication_opt_out', 'gdpr_consent_given_at', 'gdpr_consent_ip'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'hourly_rate' => 'decimal:2',
            'two_factor_enabled' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted',
            'password_changed_at' => 'datetime',
            'communication_preferences' => 'array',
            'communication_opt_out' => 'boolean',
            'gdpr_consent_given_at' => 'datetime',
        ];
    }

    public function assignedCases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'assigned_attorney_id');
    }

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(LegalCase::class, 'case_participants', 'user_id', 'case_id')->withPivot('role', 'notes');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function invoicesAsClient(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function interactionLogs(): HasMany
    {
        return $this->hasMany(InteractionLog::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function onboardings(): HasMany
    {
        return $this->hasMany(Onboarding::class);
    }

    public function surveyResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function isAttorney(): bool
    {
        return in_array($this->role, ['admin', 'attorney']);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isParalegal(): bool
    {
        return $this->role === 'paralegal';
    }

    public function isBillingManager(): bool
    {
        return $this->role === 'billing_manager';
    }

    public function canAccessCase($case): bool
    {
        if ($this->isAdmin() || $this->id === $case->assigned_attorney_id) {
            return true;
        }

        return $case->participants()->where('user_id', $this->id)->exists();
    }

    public function enableTwoFactorAuthentication(): void
    {
        $secret = Google2FA::generateSecretKey();
        $this->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
            'two_factor_recovery_codes' => json_encode($this->generateRecoveryCodes()),
        ]);
    }

    public function disableTwoFactorAuthentication(): void
    {
        $this->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
        ]);
    }

    public function getTwoFactorQrCodeUrl(): string
    {
        $google2fa = new Google2FA;

        return $google2fa->getQRCodeUrl(
            config('app.name'),
            $this->email,
            $this->two_factor_secret
        );
    }

    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(8));
        }

        return $codes;
    }
}
