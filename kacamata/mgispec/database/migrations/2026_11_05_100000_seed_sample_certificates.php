<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Two placeholder certificates so the product-page slider is visible before the real ones are uploaded.
 * The sample files ship in public/images/sample-certificates and are copied into the public disk
 * (so the CMS treats them like normal uploads). Only runs when the table is empty;
 * replace/delete them in CMS → Katalog → Sertifikat.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates') || DB::table('certificates')->exists()) {
            return;
        }

        $disk = Storage::disk('public');
        $now = now();

        foreach ([1, 2] as $i) {
            $files = [];
            foreach (['webp', 'pdf'] as $ext) {
                $src = public_path("images/sample-certificates/contoh-sertifikat-{$i}.{$ext}");
                if (! File::exists($src)) {
                    continue 2;
                }
                $files[$ext] = "certificates/contoh-sertifikat-{$i}.{$ext}";
                $disk->put($files[$ext], File::get($src));
            }

            DB::table('certificates')->insert([
                'title' => 'Contoh Sertifikat '.$i,
                'image' => $files['webp'],
                'pdf_file' => $files['pdf'],
                'sort_order' => $i,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        $rows = DB::table('certificates')->where('image', 'like', 'certificates/contoh-sertifikat-%')->get();
        foreach ($rows as $row) {
            Storage::disk('public')->delete([$row->image, $row->pdf_file]);
        }
        DB::table('certificates')->where('image', 'like', 'certificates/contoh-sertifikat-%')->delete();
    }
};
