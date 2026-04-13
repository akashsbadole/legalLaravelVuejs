<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'category', 'priority', 'message',
        'status', 'assigned_to', 'assigned_by', 'assigned_at',
        'admin_notes', 'resolved_at'
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'emergency' => 'bg-red-100 text-red-800',
            'urgent' => 'bg-orange-100 text-orange-800',
            'normal' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'new' => 'bg-blue-100 text-blue-800',
            'assigned' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getCategoryDisplay(): string
    {
        return match($this->category) {
            'civil_law' => 'Civil Law',
            'criminal_law' => 'Criminal Law',
            'corporate_law' => 'Corporate Law',
            'family_law' => 'Family Law',
            'property_law' => 'Property Law',
            'consultation' => 'General Consultation',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['urgent', 'emergency']);
    }
}
