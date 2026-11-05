<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * The first mobile poster put the MGISpec logo right under the transparent site header (the two logos overlapped).
 * The new poster sits lower. It is published under a new file name so browsers do not keep showing the cached one.
 */
return new class extends Migration
{
    private const TARGET = 'sliders/launching-poster-mobile-v2.webp';

    public function up(): void
    {
        $from = public_path('videos/launching-poster-mobile.webp');
        $row = DB::table('sliders')->where('video_path', 'videos/launching-mgispec.mp4')->first();
        if (! $row || ! File::exists($from)) {
            return;
        }

        $stream = fopen($from, 'rb');
        Storage::disk('public')->put(self::TARGET, $stream);
        is_resource($stream) && fclose($stream);

        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));
        if ($mirror) {
            File::ensureDirectoryExists(dirname($publicDir.'/'.self::TARGET));
            File::copy($from, $publicDir.'/'.self::TARGET);
        }

        $mobile = json_decode((string) $row->mobile_image, true) ?: [];
        $mobile['id'] = self::TARGET;
        DB::table('sliders')->where('id', $row->id)->update([
            'mobile_image' => json_encode($mobile),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $row = DB::table('sliders')->where('video_path', 'videos/launching-mgispec.mp4')->first();
        if ($row) {
            $mobile = json_decode((string) $row->mobile_image, true) ?: [];
            $mobile['id'] = 'sliders/launching-poster-mobile.webp';
            DB::table('sliders')->where('id', $row->id)->update(['mobile_image' => json_encode($mobile)]);
        }
        Storage::disk('public')->delete(self::TARGET);
    }
};
