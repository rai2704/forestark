<?php

namespace App\Models;

use App\Support\Content;
use App\Support\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Entry extends Model
{
    protected $fillable = ['collection', 'slug', 'data', 'is_published', 'published_at', 'position'];

    protected $casts = ['data' => 'array', 'is_published' => 'boolean', 'published_at' => 'datetime'];

    public static function definition(string $collection): array
    {
        return config("portal.collections.$collection");
    }

    public function schema(): array
    {
        return self::definition($this->collection);
    }

    public function scopeOf(Builder $query, string $collection): Builder
    {
        $query->where('collection', $collection);

        return self::definition($collection)['dated'] ?? false
            ? $query->orderByDesc('published_at')->orderByDesc('id')
            : $query->orderBy('position')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function content(?string $locale = null): array
    {
        return Content::resolve($this->schema()['fields'], $this->data ?? [], $locale);
    }

    /** Conteúdo no formato que os blocos do site esperam. */
    public function toItem(array $blockFields): array
    {
        $fields = collect($this->schema()['fields'])->concat($blockFields)->unique('name')->values()->all();

        return Content::resolve($fields, $this->data ?? []) + ['url' => $this->publicUrl()];
    }

    /** Só há página de detalhe quando o módulo prevê e o conteúdo completo foi preenchido. */
    public function publicUrl(): ?string
    {
        $route = $this->schema()['detail'] ?? null;
        if (! $route || ! $this->slug || trim(strip_tags((string) ($this->content()['body'] ?? ''))) === '') {
            return null;
        }

        return Site::url($route.'/'.$this->slug);
    }

    public static function uniqueSlug(string $collection, string $text, ?int $ignore = null): string
    {
        $base = Str::slug($text) ?: 'item';
        $slug = $base;
        $n = 2;
        while (self::where('collection', $collection)->where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }

    public function slugSource(): string
    {
        $c = $this->content(Site::defaultLocale());

        return trim(($c['title'] ?? '').' '.($c['highlight'] ?? ''));
    }
}
