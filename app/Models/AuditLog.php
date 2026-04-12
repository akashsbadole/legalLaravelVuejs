<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    
    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'description', 'old_values', 'new_values', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $entityType = null, int $entityId = null, string $description = null, array $oldValues = [], array $newValues = []): self
    {
        $request = request();
        
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public static function logCreated(string $entityType, int $entityId, array $values): void
    {
        self::log('created', $entityType, $entityId, "Created new {$entityType}", [], $values);
    }

    public static function logUpdated(string $entityType, int $entityId, array $oldValues, array $newValues): void
    {
        self::log('updated', $entityType, $entityId, "Updated {$entityType}", $oldValues, $newValues);
    }

    public static function logDeleted(string $entityType, int $entityId, array $values): void
    {
        self::log('deleted', $entityType, $entityId, "Deleted {$entityType}", $values, []);
    }

    public static function logAction(string $action, string $description): void
    {
        self::log($action, null, null, $description);
    }
}