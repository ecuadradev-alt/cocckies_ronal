<?php
namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $guard_name = 'api'; 

    public function scopeByModule($query, $module)
    {
        return $query->where('name', 'like', "$module.%");
    }

    public function belongsToModule($module)
    {
        return str_starts_with($this->name, "$module.");
    }
}