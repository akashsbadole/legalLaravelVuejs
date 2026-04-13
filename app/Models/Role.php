<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'is_default'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    public function givePermissionTo(Permission $permission)
    {
        $this->permissions()->attach($permission);
    }

    public function revokePermissionFrom(Permission $permission)
    {
        $this->permissions()->detach($permission);
    }
}
