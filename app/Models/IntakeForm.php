<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntakeForm extends Model
{
    protected $table = 'intake_forms';
    
    protected $fillable = [
        'tenant_id', 'name', 'fields_json', 'is_active', 'is_public',
        'success_message', 'redirect_url', 'notify_emails',
        'create_lead', 'assign_to', 'created_by'
    ];

    protected function casts(): array
    {
        return [
            'fields_json' => 'array',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'create_lead' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(IntakeSubmission::class, 'form_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getFieldTypes(): array
    {
        return [
            'text' => 'Text Input',
            'textarea' => 'Text Area',
            'email' => 'Email',
            'phone' => 'Phone',
            'select' => 'Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Buttons',
            'date' => 'Date Picker',
            'file' => 'File Upload',
            'address' => 'Address',
        ];
    }

    public static function getDefaultFields(): array
    {
        return [
            ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
            ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
            ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true],
            ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'phone', 'required' => true],
            ['name' => 'case_type', 'label' => 'Type of Legal Matter', 'type' => 'select', 'required' => true, 'options' => ['Civil', 'Criminal', 'Family', 'Property', 'Corporate', 'Other']],
            ['name' => 'description', 'label' => 'Brief Description', 'type' => 'textarea', 'required' => true],
            ['name' => 'preferred_contact', 'label' => 'Preferred Contact Method', 'type' => 'radio', 'options' => ['Email', 'Phone', 'WhatsApp']],
        ];
    }
}

class IntakeSubmission extends Model
{
    protected $table = 'intake_submissions';
    
    protected $fillable = [
        'form_id', 'lead_id', 'data_json', 'ip_address', 'user_agent', 'status'
    ];

    protected function casts(): array
    {
        return [
            'data_json' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(IntakeForm::class, 'form_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public static function getStatuses(): array
    {
        return [
            'new' => 'New',
            'reviewed' => 'Reviewed',
            'contacted' => 'Contacted',
            'converted' => 'Converted',
            'rejected' => 'Rejected',
        ];
    }
}
