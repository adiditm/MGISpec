<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Follow-up of 2026_11_11_100000_import_client_product_content: the client wants the Description tab to carry the full
 * Word text, including the 12 frame materials and the lens features, while the Frame / Lens Technology tabs keep showing
 * the same items as separate cards. Only the description column changes; the previous value is saved to
 * product_content_backups first and down() restores it.
 */
return new class extends Migration
{
    private const BATCH = 'client-content-2026-11-desc';

    public function up(): void
    {
        $data = json_decode((string) File::get(database_path('data/product-description-refresh.json')), true);
        if (! is_array($data)) {
            throw new RuntimeException('product-description-refresh.json tidak terbaca.');
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

        foreach ($data as $sku => $description) {
            $row = DB::table('products')->where('sku', $sku)->first();
            if (! $row) {
                continue;
            }

            if (! DB::table('product_content_backups')->where('product_id', $row->id)->where('batch', self::BATCH)->exists()) {
                DB::table('product_content_backups')->insert([
                    'product_id' => $row->id,
                    'batch' => self::BATCH,
                    'payload' => json_encode(['description' => $row->description], JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                ]);
            }

            DB::table('products')->where('id', $row->id)->update([
                'description' => json_encode($description, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
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
