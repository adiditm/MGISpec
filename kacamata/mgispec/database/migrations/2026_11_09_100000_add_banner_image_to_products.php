<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Per-model banner thumbnail shown under the breadcrumb on the product page, one image per language
 * (CMS: Produk → Foto → Banner). Without an image the banner is simply not shown.
 * The MS016 test banners (public/images/banners) are moved into the CMS here.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'banner_image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('banner_image')->nullable();
            });
        }

        $product = DB::table('products')->where('sku', 'like', '%MS016%')->orderBy('id')->get();
        if ($product->isEmpty()) {
            return;
        }

        $disk = Storage::disk('public');
        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));

        $paths = [];
        foreach (['id', 'en', 'cn'] as $code) {
            $from = public_path("images/banners/ms016-safety-glasses-{$code}.webp");
            if (! File::exists($from)) {
                continue;
            }
            $to = "products/banners/ms016-safety-glasses-{$code}.webp";
            $stream = fopen($from, 'rb');
            $disk->put($to, $stream);
            is_resource($stream) && fclose($stream);

            if ($mirror) {
                File::ensureDirectoryExists(dirname($publicDir.'/'.$to));
                File::copy($from, $publicDir.'/'.$to);
            }
            $paths[$code] = $to;
        }

        if ($paths === []) {
            return;
        }

        foreach ($product as $row) {
            if (blank($row->banner_image)) {
                DB::table('products')->where('id', $row->id)->update(['banner_image' => json_encode($paths)]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'banner_image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('banner_image');
            });
        }
    }
};
