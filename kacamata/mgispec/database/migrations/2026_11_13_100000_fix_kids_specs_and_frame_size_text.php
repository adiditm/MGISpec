<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Correction after the client-content import:
 *  - KID29 Blue and KID13 Pink had no size block parsed (bullet-prefixed lines were missed), so they kept the old sample
 *    specs (47/15/135, Material Frame, 13 g). They now carry exactly the size values from their Word files.
 *  - The standalone "FRAME SIZE" heading lines of the Word files are no longer copied into the Description tab.
 * Only the columns present in the data file per product are touched; the previous values go to product_content_backups
 * first and down() restores them.
 */
return new class extends Migration
{
    private const BATCH = 'client-content-2026-11-fix';

    public function up(): void
    {
        $data = json_decode((string) File::get(database_path('data/product-content-fix-2026-11-13.json')), true);
        if (! is_array($data)) {
            throw new RuntimeException('product-content-fix-2026-11-13.json tidak terbaca.');
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

        foreach ($data as $sku => $cols) {
            $row = DB::table('products')->where('sku', $sku)->first();
            if (! $row) {
                continue;
            }

            if (! DB::table('product_content_backups')->where('product_id', $row->id)->where('batch', self::BATCH)->exists()) {
                $old = [];
                foreach (array_keys($cols) as $col) {
                    $old[$col] = $row->{$col};
                }
                DB::table('product_content_backups')->insert([
                    'product_id' => $row->id,
                    'batch' => self::BATCH,
                    'payload' => json_encode($old, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                ]);
            }

            $update = ['updated_at' => now()];
            foreach ($cols as $col => $value) {
                $update[$col] = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            DB::table('products')->where('id', $row->id)->update($update);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_content_backups')) {
            return;
        }

        foreach (DB::table('product_content_backups')->where('batch', self::BATCH)->get() as $backup) {
            $old = json_decode($backup->payload, true);
            if (is_array($old)) {
                DB::table('products')->where('id', $backup->product_id)->update($old + ['updated_at' => now()]);
            }
        }
        DB::table('product_content_backups')->where('batch', self::BATCH)->delete();
    }
};
