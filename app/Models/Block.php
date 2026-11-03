<?php

namespace App\Models;

use App\Support\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    protected $fillable = ['page_id', 'type', 'data', 'position', 'is_visible'];

    protected $casts = ['data' => 'array', 'is_visible' => 'boolean'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function schema(): array
    {
        return config("portal.blocks.{$this->type}", ['label' => $this->type, 'fields' => []]);
    }

    public function content(): array
    {
        return Content::resolve($this->schema()['fields'], $this->data ?? []);
    }
}
