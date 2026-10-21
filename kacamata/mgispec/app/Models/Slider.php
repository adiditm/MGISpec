<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    /** Transition effects offered per slide in the CMS (key => label). */
    public const EFFECTS = [
        'slide' => 'Slide / Horizontal — bergeser ke kiri atau kanan',
        'fade' => 'Fade / Crossfade — memudar halus',
        'kenburns' => 'Zoom / Ken Burns — perlahan membesar',
        'wipe' => 'Wipe / Reveal — menyapu dari kiri ke kanan',
        'cube' => '3D Carousel / Cube — berputar 3D',
        'random' => 'Random — efek berganti acak setiap kali banner muncul',
    ];

    /** Normalise an effect name, mapping the old site-wide options onto the current ones. */
    public static function effectKey(?string $effect): string
    {
        $effect = ['parallax' => 'slide', 'depth' => 'cube', 'curtain' => 'wipe'][$effect] ?? $effect;

        return array_key_exists((string) $effect, self::EFFECTS) ? $effect : 'fade';
    }

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
