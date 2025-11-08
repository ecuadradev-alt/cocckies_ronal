<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Role extends SpatieRole
{
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
        );
    }

    public function isAdmin(): bool
    {
        return strtoupper($this->name) === 'ADMIN';
    }
}
