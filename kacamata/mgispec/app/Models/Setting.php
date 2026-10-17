<?php

namespace App\Models;

use App\Support\I18n;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    protected $casts = ['value' => 'array'];

    public const DEFAULTS = [
        'whatsapp' => '6281234567890',
        'phone' => '+62 812-3456-7890',
        'email' => 'info@mgispec.com',
        'address' => 'Jakarta & Manila Regional Representative',
        'facebook' => 'https://facebook.com',
        'instagram' => '',
        'copyright' => '© 2026 - MGISPEC @ MGIClub Global. All Rights Reserved',
        'footer_about' => [
            'id' => 'MGISpec merupakan kacamata medis berteknologi nanopartikel ion negatif dan oksigen untuk menjaga kesehatan mata dari radiasi gadget dan kelelahan visual.',
            'ph' => 'Ang MGISpec ay makabagong medikal na salamin na may nanoparticle technology upang maprotektahan ang mga mata sa radiation at visual fatigue.',
        ],
        'slider_effect' => 'fade',
        'slider_delay' => 6,
    ];

    /** All settings merged over defaults (raw, not localized). */
    public static function values(): array
    {
        return Cache::rememberForever('site_settings', function () {
            $stored = static::query()->pluck('value', 'key')->all();

            return array_replace(self::DEFAULTS, array_filter($stored, fn ($v) => $v !== null && $v !== ''));
        });
    }

    /** One setting, localized when it is a bilingual value. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return I18n::pick(static::values()[$key] ?? $default);
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('site_settings');
    }
}
