<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'description', 'module'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Role::class, 'role_permissions');
    }

    public static function getGroupedPermissions()
    {
        return self::all()->groupBy('module');
    }
}
