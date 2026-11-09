<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * Instagram and TikTok accounts from the client (CMS: Pengaturan > Media Sosial). The share-link tracking
 * parameters (?_r=..., ?vrfl=...) are dropped; the links point at the same profiles. Existing CMS values are kept.
 */
return new class extends Migration
{
    private const LINKS = [
        'instagram' => 'https://www.instagram.com/mgiclubindonesia',
        'tiktok' => 'https://www.tiktok.com/@mgiclub.official',
    ];

    public function up(): void
    {
        $current = Setting::values();
        $set = [];
        foreach (self::LINKS as $key => $url) {
            if (blank($current[$key] ?? null)) {
                $set[$key] = $url;
            }
        }

        if ($set !== []) {
            Setting::put($set);
        }
    }

    public function down(): void
    {
        Setting::query()->whereIn('key', ['tiktok'])->delete();
        Setting::put(['instagram' => null]);
    }
};
