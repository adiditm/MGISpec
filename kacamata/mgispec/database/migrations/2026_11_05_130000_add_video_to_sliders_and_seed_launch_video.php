<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Hero slides can carry a video: the banner image becomes the poster and a play button opens the player.
 * Also adds the "Launching MGISpec" video as the first slide. The source files ship in public/videos and are
 * copied into the public disk (and into public/storage when that is a real folder, as on the dev server).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sliders', 'video_path')) {
            Schema::table('sliders', function (Blueprint $table) {
                $table->string('video_path')->nullable()->after('mobile_image');
            });
        }

        if (DB::table('sliders')->where('video_path', 'videos/launching-mgispec.mp4')->exists()) {
            return;
        }

        $files = [
            'videos/launching-mgispec.mp4' => 'launching-mgispec.mp4',
            'sliders/launching-poster-desktop.webp' => 'launching-poster-desktop.webp',
            'sliders/launching-poster-mobile.webp' => 'launching-poster-mobile.webp',
        ];

        $disk = Storage::disk('public');
        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));

        foreach ($files as $to => $name) {
            $from = public_path('videos/'.$name);
            if (! File::exists($from)) {
                return;
            }
            $stream = fopen($from, 'rb');
            $disk->put($to, $stream);
            is_resource($stream) && fclose($stream);

            if ($mirror) {
                File::ensureDirectoryExists(dirname($publicDir.'/'.$to));
                File::copy($from, $publicDir.'/'.$to);
            }
        }

        DB::table('sliders')->insert([
            'title' => json_encode([
                'id' => 'Video Peluncuran MGISpec',
                'en' => 'MGISpec Launch Video',
                'cn' => 'MGISpec 发布视频',
            ], JSON_UNESCAPED_UNICODE),
            'desktop_image' => json_encode(['id' => 'sliders/launching-poster-desktop.webp']),
            'mobile_image' => json_encode(['id' => 'sliders/launching-poster-mobile.webp']),
            'video_path' => 'videos/launching-mgispec.mp4',
            'show_text' => false,
            'text_position' => 'left',
            'overlay' => 0,
            'effect' => 'fade',
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $row = DB::table('sliders')->where('video_path', 'videos/launching-mgispec.mp4')->first();
        if ($row) {
            Storage::disk('public')->delete([
                'videos/launching-mgispec.mp4',
                'sliders/launching-poster-desktop.webp',
                'sliders/launching-poster-mobile.webp',
            ]);
            DB::table('sliders')->where('id', $row->id)->delete();
        }

        if (Schema::hasColumn('sliders', 'video_path')) {
            Schema::table('sliders', fn (Blueprint $table) => $table->dropColumn('video_path'));
        }
    }
};
