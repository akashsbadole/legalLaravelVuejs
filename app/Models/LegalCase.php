<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LegalCase extends Model
{
    protected $table = 'cases';
    
    protected $fillable = [
        'case_number', 'title', 'description', 'case_type', 'status_id',
        'assigned_attorney_id', 'open_date', 'expected_close_date',
        'actual_close_date', 'estimated_value', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'open_date' => 'date',
            'expected_close_date' => 'date',
            'actual_close_date' => 'date',
            'estimated_value' => 'decimal:2',
        ];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CaseStatus::class);
    }

    public function assignedAttorney(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_attorney_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'case_participants', 'case_id', 'user_id')->withPivot('role', 'notes');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public static function generateCaseNumber(): string
    {
        $year = date('Y');
        $lastCase = self::where('case_number', 'like', "CASE-{$year}%")->orderBy('case_number', 'desc')->first();
        $sequence = $lastCase ? (int)substr($lastCase->case_number, -4) + 1 : 1;
        return sprintf('CASE-%s-%04d', $year, $sequence);
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->timeEntries()->where('status', 'approved')->sum('hours');
    }

    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses()->where('status', 'approved')->sum('amount');
    }

    public function getTotalBilledAttribute(): float
    {
        return $this->invoices()->sum('total_amount');
    }
}