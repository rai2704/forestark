<?php

namespace App\Models;

use App\Support\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = ['slug', 'data', 'is_published'];

    protected $casts = ['data' => 'array', 'is_published' => 'boolean'];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('position')->orderBy('id');
    }

    public function content(): array
    {
        return Content::resolve(config('portal.page_fields'), $this->data ?? []);
    }
}
