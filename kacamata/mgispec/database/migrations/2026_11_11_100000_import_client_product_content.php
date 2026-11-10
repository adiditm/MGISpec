<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Product content from the client's "Web Content MGISPEC" pack (Word files in Indonesian / English / Mandarin, 800 x 650
 * gallery photos and the 3:1 product banners). It replaces the placeholder texts and photos that were entered earlier:
 *   description, specs (only when the Word file has them), frame_technologies (12 materials), lens_technologies (4 or 9
 *   features), thumbnail, gallery and banner_image.
 * Kept as they are: name, SKU, category, order, badge, highlights, short description and the catalog PDFs.
 *
 * The values we overwrite are copied to product_content_backups first, so down() can put them back. Photos get new file
 * names (mgi-XX.webp) and the old files stay on disk.
 */
return new class extends Migration
{
    private const COLUMNS = ['description', 'specs', 'frame_technologies', 'lens_technologies', 'thumbnail', 'gallery', 'banner_image'];

    public function up(): void
    {
        $dataFile = database_path('data/product-content-import.json');
        $zipFile = public_path('content-import/product-media.zip');
        $data = json_decode((string) File::get($dataFile), true);
        if (! is_array($data)) {
            throw new RuntimeException('product-content-import.json tidak terbaca.');
        }

        if (! Schema::hasTable('product_content_backups')) {
            Schema::create('product_content_backups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('batch', 60)->index();
                $table->longText('payload');
                $table->timestamp('created_at')->nullable();
            });
        }

        $this->extractMedia($zipFile);

        $batch = 'client-content-2026-11';
        foreach ($data as $sku => $new) {
            $row = DB::table('products')->where('sku', $sku)->first();
            if (! $row) {
                continue;
            }

            if (! DB::table('product_content_backups')->where('product_id', $row->id)->where('batch', $batch)->exists()) {
                DB::table('product_content_backups')->insert([
                    'product_id' => $row->id,
                    'batch' => $batch,
                    'payload' => json_encode(collect(self::COLUMNS)->mapWithKeys(fn ($c) => [$c => $row->{$c}])->all(), JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                ]);
            }

            $update = [
                'description' => json_encode($new['description'], JSON_UNESCAPED_UNICODE),
                'frame_technologies' => json_encode($new['frame_technologies'], JSON_UNESCAPED_UNICODE),
                'lens_technologies' => json_encode($new['lens_technologies'], JSON_UNESCAPED_UNICODE),
                'thumbnail' => $new['thumbnail'],
                'gallery' => json_encode($new['gallery']),
                'banner_image' => json_encode($new['banner_image']),
                'updated_at' => now(),
            ];
            // the Word file of a few products has no size table; those keep the specs they had
            if (! empty($new['specs'])) {
                $update['specs'] = json_encode($new['specs'], JSON_UNESCAPED_UNICODE);
            }
            DB::table('products')->where('id', $row->id)->update($update);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_content_backups')) {
            return;
        }

        foreach (DB::table('product_content_backups')->where('batch', 'client-content-2026-11')->get() as $backup) {
            $old = json_decode($backup->payload, true);
            if (is_array($old)) {
                DB::table('products')->where('id', $backup->product_id)->update($old + ['updated_at' => now()]);
            }
        }
        DB::table('product_content_backups')->where('batch', 'client-content-2026-11')->delete();
    }

    /** Copies every photo of the zip to the public disk (and into public/storage when that is a real folder, like on the dev server). */
    private function extractMedia(string $zipFile): void
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('PHP-extension zip belum aktif di server ini.');
        }
        $zip = new ZipArchive;
        if ($zip->open($zipFile) !== true) {
            throw new RuntimeException('product-media.zip tidak ditemukan di public/content-import.');
        }

        $disk = Storage::disk('public');
        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_ends_with($name, '/') || str_contains($name, '..')) {
                continue;
            }
            $disk->put($name, (string) $zip->getFromIndex($i));
            if ($mirror) {
                File::ensureDirectoryExists(dirname($publicDir.'/'.$name));
                File::put($publicDir.'/'.$name, (string) $zip->getFromIndex($i));
            }
        }
        $zip->close();
    }
};
