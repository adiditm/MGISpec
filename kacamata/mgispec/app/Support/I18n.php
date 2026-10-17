<?php

namespace App\Support;

class I18n
{
    public const LOCALES = [
        'id' => ['label' => 'Indonesia', 'short' => 'ID', 'flag' => '🇮🇩', 'enabled' => true],
        'en' => ['label' => 'English', 'short' => 'EN', 'flag' => '🇬🇧', 'enabled' => true],
        'cn' => ['label' => '中文', 'short' => 'CN', 'flag' => '🇨🇳', 'enabled' => false],
    ];

    /** Locales currently active in the front-end language switcher. */
    public static function enabledLocales(): array
    {
        return array_filter(self::LOCALES, fn ($meta) => ! empty($meta['enabled']));
    }

    /** Current-locale text from a ['id' => .., 'en' => .., 'cn' => ..] value, falling back to Indonesian. */
    public static function pick(mixed $value, ?string $locale = null): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $locale ??= app()->getLocale();

        if (filled($value[$locale] ?? null)) {
            return $value[$locale];
        }

        if ($locale === 'en' && filled($value['ph'] ?? null)) {
            return $value['ph'];
        }

        return $value[config('app.fallback_locale', 'id')] ?? reset($value) ?? null;
    }

    /**
     * Localize a CMS-entered link: "/id/katalog" or "/ph/katalog" follow the visitor's language,
     * "katalog" becomes "/{locale}/katalog", absolute URLs and anchors are left alone.
     */
    public static function link(?string $url): ?string
    {
        if (blank($url) || preg_match('#^([a-z]+:|//|\#)#i', $url)) {
            return $url;
        }

        $path = ltrim($url, '/');
        $first = strtok($path, '/?#');

        if (array_key_exists($first, self::LOCALES)) {
            $path = substr($path, strlen($first) + 1) ?: '';
        }

        return url(app()->getLocale().($path !== '' && ! str_starts_with($path, '?') ? '/' : '').$path);
    }

    /** The current URL with its leading locale segment swapped. */
    public static function switchUrl(string $locale): string
    {
        $segments = request()->segments();

        if (isset($segments[0]) && array_key_exists($segments[0], self::LOCALES)) {
            $segments[0] = $locale;
        } else {
            array_unshift($segments, $locale);
        }

        $query = request()->getQueryString();

        return url(implode('/', $segments)).($query ? '?'.$query : '');
    }
}
