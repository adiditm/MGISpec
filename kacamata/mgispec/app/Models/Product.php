<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected $casts = [
        'name' => 'array',
        'short_description' => 'array',
        'description' => 'array',
        'gallery' => 'array',
        'highlights' => 'array',
        'specs' => 'array',
        'technologies' => 'array',
        'frame_technologies' => 'array',
        'lens_technologies' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const BADGE_COLORS = [
        'pink' => 'Pink',
        'orange' => 'Orange',
        'blue' => 'Biru',
        'green' => 'Hijau',
        'purple' => 'Ungu',
        'dark' => 'Hitam',
    ];

    /**
     * The standard MGISpec frame materials (icon, title, description), taken from the
     * "Performa Unggul" material list on the Beranda so both stay the same. Used to fill
     * a product's frame technologies in one go.
     */
    public static function frameTechnologyPreset(): array
    {
        return PromoBanner::active()->get()
            ->map(fn (PromoBanner $m) => [
                'icon' => $m->icon ?: 'fa-solid fa-atom',
                'title' => $m->title,
                'description' => $m->description,
            ])
            ->values()->all();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /** Thumbnail first, then gallery images, de-duplicated, as URLs. */
    public function imageUrls(): array
    {
        return collect([$this->thumbnail, ...($this->gallery ?? [])])
            ->filter()->unique()->map(fn ($p) => Media::url($p))->values()->all();
    }
}
