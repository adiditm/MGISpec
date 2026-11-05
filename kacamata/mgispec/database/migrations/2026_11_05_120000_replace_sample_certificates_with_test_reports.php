<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Replaces the two "Contoh Sertifikat" placeholders with the 17 Zhongke analysis reports supplied by the client
 * (images = cover page of each PDF). Source files ship in public/images/certificates-src and are copied into the
 * public disk (and into public/storage when that is a real folder, as on the shared-hosting dev server).
 * Safe to re-run: reports that already exist are skipped.
 */
return new class extends Migration
{
    private const REPORTS = [
        1 => ['zk1804-01-germanite', 'Germanite Analysis Report'],
        2 => ['zk1804-02-tourmaline', 'Tourmaline Analysis Report'],
        3 => ['zk1804-03-bamboo-charcoal', 'Bamboo Charcoal Analysis Report'],
        4 => ['zk1804-04-pa6', 'PA6 Analysis Report'],
        5 => ['zk1804-05-pigment', 'Pigment Analysis Report'],
        6 => ['zk1804-06-polyurethane-pu', 'Polyurethane (PU) Analysis Report'],
        7 => ['zk1804-07-alumina', 'Alumina Analysis Report'],
        8 => ['zk1804-08-barium-sulfate', 'Barium Sulfate Analysis Report'],
        9 => ['zk1804-09-zinc-oxide', 'Zinc Oxide Analysis Report'],
        10 => ['zk1804-10-titanium-dioxide', 'Titanium Dioxide Analysis Report'],
        11 => ['zk1804-11-calcium-oxide', 'Calcium Oxide Analysis Report'],
        12 => ['zk1804-12-phosphate', 'Phosphate Analysis Report'],
        13 => ['zk1804-13-silicon-dioxide', 'Silicon Dioxide Analysis Report'],
        14 => ['zk1804-14-silver', 'Silver Analysis Report'],
        15 => ['zk1804-15-graphene', 'Graphene Analysis Report'],
        16 => ['zk1804-16-zirconia', 'Zirconia Analysis Report'],
        17 => ['zk1804-17-optical-frame', 'Optical Frame Analysis Report'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        $disk = Storage::disk('public');
        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));

        // 1) drop the placeholders
        $samples = DB::table('certificates')->where('image', 'like', 'certificates/contoh-sertifikat-%')->get();
        foreach ($samples as $row) {
            $disk->delete([$row->image, $row->pdf_file]);
            if ($mirror) {
                File::delete([$publicDir.'/'.$row->image, $publicDir.'/'.$row->pdf_file]);
            }
        }
        DB::table('certificates')->where('image', 'like', 'certificates/contoh-sertifikat-%')->delete();

        // 2) add the reports
        $now = now();
        foreach (self::REPORTS as $n => [$slug, $title]) {
            $paths = ['image' => "certificates/{$slug}.webp", 'pdf_file' => "certificates/{$slug}.pdf"];

            if (DB::table('certificates')->where('image', $paths['image'])->exists()) {
                continue;
            }

            foreach (['webp', 'pdf'] as $ext) {
                $from = public_path("images/certificates-src/{$slug}.{$ext}");
                if (! File::exists($from)) {
                    continue 2;
                }
                $to = "certificates/{$slug}.{$ext}";
                $disk->put($to, File::get($from));
                if ($mirror) {
                    File::ensureDirectoryExists($publicDir.'/certificates');
                    File::copy($from, $publicDir.'/'.$to);
                }
            }

            DB::table('certificates')->insert($paths + [
                'title' => $title,
                'sort_order' => $n,
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

        $disk = Storage::disk('public');
        foreach (DB::table('certificates')->where('image', 'like', 'certificates/zk1804-%')->get() as $row) {
            $disk->delete([$row->image, $row->pdf_file]);
        }
        DB::table('certificates')->where('image', 'like', 'certificates/zk1804-%')->delete();
    }
};
