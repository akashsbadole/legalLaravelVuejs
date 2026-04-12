<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'address', 'hourly_rate', 'is_active', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_enabled'];

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
        ];
    }

    public function assignedCases(): HasMany
    {
        return $this->hasMany(\App\Models\LegalCase::class, 'assigned_attorney_id');
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

    public function invoicesAsClient(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function isAttorney(): bool
    {
        return in_array($this->role, ['admin', 'attorney']);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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
        $secret = \PragmaRX\Google2FA\Google2FA::generateSecretKey();
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
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
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
            $codes[] = strtoupper(\Illuminate\Support\Str::random(8));
        }
        return $codes;
    }
}
