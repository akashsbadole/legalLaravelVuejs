<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use BelongsToTenant;
    protected $table = 'leads';

    protected $fillable = [
        'name', 'email', 'phone', 'company', 'description', 'assigned_to',
        'source', 'status', 'priority', 'estimated_value', 'next_follow_up',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'email' => 'encrypted',
            'phone' => 'encrypted',
            'company' => 'encrypted',
            'description' => 'encrypted',
            'estimated_value' => 'decimal:2',
            'next_follow_up' => 'date',
        ];
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public static function getStatuses(): array
    {
        return [
            'new' => 'New',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'proposal' => 'Proposal',
            'won' => 'Won',
            'lost' => 'Lost',
        ];
    }

    public static function getSources(): array
    {
        return [
            'website' => 'Website',
            'referral' => 'Referral',
            'cold_call' => 'Cold Call',
            'advertisement' => 'Advertisement',
            'other' => 'Other',
        ];
    }
}

class LeadActivity extends Model
{
    protected $table = 'lead_activities';

    protected $fillable = ['lead_id', 'user_id', 'activity_type', 'notes'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
