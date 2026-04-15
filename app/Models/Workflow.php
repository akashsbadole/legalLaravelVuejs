<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    protected $table = 'workflows';

    protected $fillable = [
        'tenant_id', 'name', 'trigger_type', 'conditions_json',
        'actions_json', 'is_active', 'priority', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'conditions_json' => 'array',
            'actions_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class, 'workflow_id');
    }

    public static function getTriggerTypes(): array
    {
        return [
            'case.created' => 'Case Created',
            'case.status_changed' => 'Case Status Changed',
            'case.assigned' => 'Case Assigned',
            'task.created' => 'Task Created',
            'task.completed' => 'Task Completed',
            'invoice.created' => 'Invoice Created',
            'invoice.paid' => 'Invoice Paid',
            'lead.created' => 'Lead Created',
            'lead.converted' => 'Lead Converted',
            'document.uploaded' => 'Document Uploaded',
            'form.submitted' => 'Form Submitted',
        ];
    }

    public function shouldTrigger(array $context): bool
    {
        $trigger = $context['trigger'] ?? '';

        if ($trigger !== $this->trigger_type) {
            return false;
        }

        if (empty($this->conditions_json)) {
            return true;
        }

        foreach ($this->conditions_json as $condition) {
            $field = $condition['field'] ?? '';
            $operator = $condition['operator'] ?? 'equals';
            $value = $condition['value'] ?? null;
            $contextValue = $context[$field] ?? null;

            if (! $this->evaluateCondition($contextValue, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    private function evaluateCondition($value, string $operator, $target): bool
    {
        return match ($operator) {
            'equals' => $value == $target,
            'not_equals' => $value != $target,
            'contains' => is_string($value) && str_contains($value, $target),
            'greater_than' => $value > $target,
            'less_than' => $value < $target,
            'is_empty' => empty($value),
            'is_not_empty' => ! empty($value),
            'in' => is_array($target) && in_array($value, $target),
            default => true,
        };
    }
}

class WorkflowExecution extends Model
{
    protected $table = 'workflow_executions';

    protected $fillable = [
        'workflow_id', 'trigger_type', 'context_json', 'status',
        'started_at', 'completed_at', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'context_json' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'running' => 'Running',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];
    }
}
