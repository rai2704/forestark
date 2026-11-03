<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'full_access', 'permissions'];

    protected $casts = ['full_access' => 'boolean', 'permissions' => 'array'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
