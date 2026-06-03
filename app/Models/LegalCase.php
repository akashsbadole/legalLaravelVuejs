<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalCase extends Model
{
    use HasFactory, BelongsToTenant;
    protected $table = 'cases';

    protected $fillable = [
        'case_number', 'title', 'description', 'case_type', 'status_id',
        'assigned_attorney_id', 'client_id', 'open_date', 'expected_close_date',
        'actual_close_date', 'estimated_value', 'notes', 'hourly_rate',
        'assigned_staff', 'assignment_changed_at', 'assignment_changed_by',
        'encrypted_data',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'encrypted',
            'description' => 'encrypted',
            'notes' => 'encrypted',
            'open_date' => 'date',
            'expected_close_date' => 'date',
            'actual_close_date' => 'date',
            'estimated_value' => 'decimal:2',
            'assigned_staff' => 'array',
            'assignment_changed_at' => 'datetime',
            'encrypted_data' => 'encrypted:json',
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

    public function assignedStaff()
    {
        return User::whereIn('id', $this->assigned_staff ?? [])->get();
    }

    public function assignmentChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignment_changed_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'case_participants', 'case_id', 'user_id')->withPivot('role', 'notes');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class, 'case_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'case_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class, 'case_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'case_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'case_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CaseTask::class, 'case_id');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(CaseTimelineEvent::class, 'case_id')->orderBy('event_date', 'asc');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(CaseFollowUp::class, 'case_id');
    }

    public static function generateCaseNumber(): string
    {
        $year = date('Y');
        $lastCase = self::where('case_number', 'like', "CASE-{$year}%")->orderBy('case_number', 'desc')->first();
        $sequence = $lastCase ? (int) substr($lastCase->case_number, -4) + 1 : 1;

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

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function getClientUserAttribute(): ?User
    {
        return $this->participants()->wherePivot('role', 'client')->first();
    }

    public function getClientNameAttribute(): ?string
    {
        $client = $this->getClientUserAttribute();

        return $client?->name;
    }
}
