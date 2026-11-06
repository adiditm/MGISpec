<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * The launch video moves out of the hero slider into its own block between the hero and "Material Canggih"
 * (CMS: Beranda → Video Beranda). The video file is already on the public disk from the earlier slider migration;
 * this adds the wide poster, points the new settings at them and removes the video slide.
 */
return new class extends Migration
{
    private const VIDEO = 'videos/launching-mgispec.mp4';

    private const POSTER = 'videos/launching-poster-wide.webp';

    public function up(): void
    {
        $disk = Storage::disk('public');

        // 1. wide poster -> public disk (+ the real public/storage folder when it is not a symlink, as on the dev server)
        $poster = public_path('videos/launching-poster-wide.webp');
        if (File::exists($poster)) {
            $stream = fopen($poster, 'rb');
            $disk->put(self::POSTER, $stream);
            is_resource($stream) && fclose($stream);

            $publicDir = public_path('storage');
            $mirror = is_dir($publicDir) && ! is_link($publicDir)
                && realpath($publicDir) !== realpath(storage_path('app/public'));
            if ($mirror) {
                File::ensureDirectoryExists($publicDir.'/videos');
                File::copy($poster, $publicDir.'/'.self::POSTER);
            }
        }

        // 2. the video itself is expected from the slider migration; stay quiet when it is missing
        if (! $disk->exists(self::VIDEO)) {
            return;
        }

        // 3. do not overwrite a video somebody already picked in the CMS
        $current = Setting::query()->where('key', 'home_video_path')->value('value');
        if (blank($current)) {
            Setting::put([
                'home_video_enabled' => true,
                'home_video_path' => self::VIDEO,
                'home_video_poster' => $disk->exists(self::POSTER) ? self::POSTER : null,
            ]);
        }

        // 4. remove the old first slide (the video slide); the slider keeps its video feature
        DB::table('sliders')->where('video_path', self::VIDEO)->delete();
    }

    public function down(): void
    {
        Setting::query()->whereIn('key', ['home_video_enabled', 'home_video_path', 'home_video_poster'])->delete();
        \Illuminate\Support\Facades\Cache::forget('site_settings');
        Storage::disk('public')->delete(self::POSTER);
    }
};
