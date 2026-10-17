<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected $casts = [
        'badge' => 'array',
        'title' => 'array',
        'subtitle' => 'array',
        'cta_text' => 'array',
        'cta2_text' => 'array',
        'show_text' => 'boolean',
        'is_active' => 'boolean',
        'overlay' => 'integer',
    ];

    public function getDesktopImageAttribute(mixed $value): mixed
    {
        if (is_array($value) || is_null($value)) {
            return $value;
        }
        $decoded = json_decode((string) $value, true);
        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : $value;
    }

    public function setDesktopImageAttribute(mixed $value): void
    {
        $this->attributes['desktop_image'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getMobileImageAttribute(mixed $value): mixed
    {
        if (is_array($value) || is_null($value)) {
            return $value;
        }
        $decoded = json_decode((string) $value, true);
        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : $value;
    }

    public function setMobileImageAttribute(mixed $value): void
    {
        $this->attributes['mobile_image'] = is_array($value) ? json_encode($value) : $value;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
