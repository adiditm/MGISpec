<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Mandarin catalog PDFs ("MGISPEC CATALOG SLIDER - CHINESE" from the client). For every product that already has an
 * Indonesian/English catalog (catalogs/<name>-id.pdf), the matching catalogs/<name>-cn.pdf shipped in public/catalogs
 * is copied to the public disk and set as the Chinese catalog. A Chinese file already chosen in the CMS is kept.
 */
return new class extends Migration
{
    public function up(): void
    {
        $disk = Storage::disk('public');
        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));

        $copied = [];
        foreach (DB::table('products')->whereNotNull('catalog_pdf')->get() as $row) {
            $pdf = json_decode($row->catalog_pdf, true);
            if (! is_array($pdf) || filled($pdf['cn'] ?? null)) {
                continue;
            }

            $source = $pdf['id'] ?? $pdf['en'] ?? null;
            if (! is_string($source) || ! preg_match('#^catalogs/(.+)-(id|en)\.pdf$#', $source, $m)) {
                continue;
            }

            $path = 'catalogs/'.$m[1].'-cn.pdf';
            $from = public_path($path);
            if (! File::exists($from)) {
                continue;
            }

            if (! isset($copied[$path])) {
                $stream = fopen($from, 'rb');
                $disk->put($path, $stream);
                is_resource($stream) && fclose($stream);
                if ($mirror) {
                    File::ensureDirectoryExists(dirname($publicDir.'/'.$path));
                    File::copy($from, $publicDir.'/'.$path);
                }
                $copied[$path] = true;
            }

            $pdf['cn'] = $path;
            DB::table('products')->where('id', $row->id)->update(['catalog_pdf' => json_encode($pdf)]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('products')->whereNotNull('catalog_pdf')->get() as $row) {
            $pdf = json_decode($row->catalog_pdf, true);
            if (is_array($pdf) && is_string($pdf['cn'] ?? null) && str_ends_with($pdf['cn'], '-cn.pdf')) {
                unset($pdf['cn']);
                DB::table('products')->where('id', $row->id)->update(['catalog_pdf' => json_encode($pdf)]);
            }
        }
    }
};
