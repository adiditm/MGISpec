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
        'whatsapp' => '6282128815099',
        'phone' => '+62 821-2881-5099',
        'email' => 'info@mgispec.com',
        'address' => 'Jakarta & Manila Regional Representative',
        'facebook' => 'https://www.facebook.com/mgiclubglobalHQ/',
        'instagram' => '',
        'copyright' => '© 2026 - MGISPEC @ MGIClub Global. All Rights Reserved',
        'footer_about' => [
            'id' => 'MGISPEC menggabungkan rekayasa optik canggih dengan material fungsional inovatif yang terinspirasi dari alam untuk memberikan kenyamanan visual dan performa yang optimal. Setiap frame MGISPEC dirancang menggunakan teknologi nanokomposit canggih dan diperkaya dengan material fungsional eksklusif. Konstruksi inovatif ini meningkatkan daya tahan, kenyamanan, dan stabilitas. Dirancang untuk Performa Optimal dalam Penggunaan Sehari-hari.',
            'en' => 'MGISPEC combines advanced optical engineering with innovative functional materials inspired by nature to deliver exceptional visual comfort and performance. Every MGISPEC frame is engineered using advanced nanocomposite technology and infused with proprietary functional materials. This innovative construction enhances durability, comfort, and long-term stability for everyday wear.',
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
