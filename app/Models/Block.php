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

    /** Módulo de onde o bloco puxa os itens, ou null se os itens são do próprio bloco. */
    public function source(): ?string
    {
        $source = $this->data['source'] ?? null;

        return $source && config("portal.collections.$source") ? $source : null;
    }

    public function schema(): array
    {
        return config("portal.blocks.{$this->type}", ['label' => $this->type, 'fields' => []]);
    }

    public function content(): array
    {
        $out = Content::resolve($this->schema()['fields'], $this->data ?? []);

        foreach ($this->schema()['fields'] as $field) {
            if ($field['type'] === 'repeater') {
                $out[$field['name']] = array_values(array_filter($out[$field['name']] ?? [], fn ($item) => empty($item['hidden'])));
            }
        }

        if ($source = $this->source()) {
            $itemFields = collect($this->schema()['fields'])->firstWhere('name', 'items')['fields'] ?? [];
            $out['items'] = Entry::of($source)->published()->get()->map(fn (Entry $e) => $e->toItem($itemFields))->all();
        }

        return $out;
    }
}
