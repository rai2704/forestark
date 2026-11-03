<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['data'];

    protected $casts = ['data' => 'array'];

    public static function current(): self
    {
        return self::query()->first() ?? self::create(['data' => []]);
    }
}
