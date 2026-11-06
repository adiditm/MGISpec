<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Mandarin banners for hero slides 1-5 (material from the client, "MGISPEC SLIDER - CHINESE").
 * Adds a "cn" image next to the existing "id" and "en" ones; slides are matched by their Indonesian image name.
 */
return new class extends Migration
{
    public function up(): void
    {
        $disk = Storage::disk('public');
        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));

        for ($n = 1; $n <= 5; $n++) {
            $row = DB::table('sliders')->where('desktop_image', 'like', '%slider-'.$n.'-desktop-id.webp%')->first();
            if (! $row) {
                continue;
            }

            $paths = [];
            foreach (['desktop', 'mobile'] as $kind) {
                $name = "slider-{$n}-{$kind}-cn.webp";
                $from = public_path('slider-cn/'.$name);
                if (! File::exists($from)) {
                    continue 2;
                }
                $stream = fopen($from, 'rb');
                $disk->put('sliders/'.$name, $stream);
                is_resource($stream) && fclose($stream);
                if ($mirror) {
                    File::ensureDirectoryExists($publicDir.'/sliders');
                    File::copy($from, $publicDir.'/sliders/'.$name);
                }
                $paths[$kind] = 'sliders/'.$name;
            }

            $update = ['updated_at' => now()];
            foreach (['desktop', 'mobile'] as $kind) {
                $col = $kind.'_image';
                $val = json_decode((string) $row->{$col}, true) ?: [];
                $val['cn'] = $paths[$kind];
                $update[$col] = json_encode($val);
            }
            DB::table('sliders')->where('id', $row->id)->update($update);
        }
    }

    public function down(): void
    {
        foreach (DB::table('sliders')->get() as $row) {
            $update = [];
            foreach (['desktop_image', 'mobile_image'] as $col) {
                $val = json_decode((string) $row->{$col}, true);
                if (is_array($val) && isset($val['cn'])) {
                    unset($val['cn']);
                    $update[$col] = json_encode($val);
                }
            }
            $update && DB::table('sliders')->where('id', $row->id)->update($update);
        }
        for ($n = 1; $n <= 5; $n++) {
            Storage::disk('public')->delete(["sliders/slider-{$n}-desktop-cn.webp", "sliders/slider-{$n}-mobile-cn.webp"]);
        }
    }
};
