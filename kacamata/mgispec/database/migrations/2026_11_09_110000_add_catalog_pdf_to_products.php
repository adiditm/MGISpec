<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Per-model catalog PDF (one per language) behind the "Unduh Katalog" button on the product page
 * (CMS: Produk > Foto > Katalog PDF). The Indonesian and English PDFs from the client's "Web Content MGISPEC"
 * package ship in public/catalogs and are copied into the public disk (and into public/storage when that is a
 * real folder, as on the dev server). Chinese catalogs follow later: until then Chinese visitors get the English PDF.
 * A product without any catalog simply has no download button.
 */
return new class extends Migration
{
    /** @var array<string, array{id: string, en: string}> */
    private const CATALOGS = [
        'MGI-KID29-BLU' => ['id' => 'catalogs/mgispec-kid-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-kid-series-catalog-en.pdf'],
        'MGI-KID13-PNK' => ['id' => 'catalogs/mgispec-kid-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-kid-series-catalog-en.pdf'],
        'MGI-MS016-BLK' => ['id' => 'catalogs/mgispec-ms016-safety-glass-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms016-safety-glass-catalog-en.pdf'],
        'MGI-MS131-BLK' => ['id' => 'catalogs/mgispec-ms131-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms131-catalog-en.pdf'],
        'MGI-MS131-BRN' => ['id' => 'catalogs/mgispec-ms131-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms131-catalog-en.pdf'],
        'MGI-MS138B-RED' => ['id' => 'catalogs/mgispec-ms138b-red-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms138b-red-catalog-en.pdf'],
        'MGI-MS14-GRY' => ['id' => 'catalogs/mgispec-ms14-grey-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms14-grey-catalog-en.pdf'],
        'MGI-MS161-COF' => ['id' => 'catalogs/mgispec-ms161-coffee-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms161-coffee-catalog-en.pdf'],
        'MGI-MS191-LEO' => ['id' => 'catalogs/mgispec-ms191-leopard-print-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms191-leopard-print-catalog-en.pdf'],
        'MGI-MS28-BLK' => ['id' => 'catalogs/mgispec-ms28-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms28-catalog-en.pdf'],
        'MGI-MS28-TEA' => ['id' => 'catalogs/mgispec-ms28-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms28-catalog-en.pdf'],
        'MGI-MS35-BLK' => ['id' => 'catalogs/mgispec-ms35-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms35-catalog-en.pdf'],
        'MGI-MS35-RED' => ['id' => 'catalogs/mgispec-ms35-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms35-catalog-en.pdf'],
        'MGI-MS41-GLS' => ['id' => 'catalogs/mgispec-ms41-matte-black-gloss-black-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms41-matte-black-gloss-black-catalog-en.pdf'],
        'MGI-MS41-BLK' => ['id' => 'catalogs/mgispec-ms41-matte-black-gloss-black-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms41-matte-black-gloss-black-catalog-en.pdf'],
        'MGI-MS44-BLK' => ['id' => 'catalogs/mgispec-ms44-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms44-catalog-en.pdf'],
        'MGI-MS44-GRN' => ['id' => 'catalogs/mgispec-ms44-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms44-catalog-en.pdf'],
        'MGI-MS45-GRN' => ['id' => 'catalogs/mgispec-ms45-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms45-catalog-en.pdf'],
        'MGI-MS45-RED' => ['id' => 'catalogs/mgispec-ms45-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms45-catalog-en.pdf'],
        'MGI-MS610-WHT' => ['id' => 'catalogs/mgispec-ms610-white-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms610-white-catalog-en.pdf'],
        'MGI-MS67-BLK' => ['id' => 'catalogs/mgispec-ms67-black-and-blue-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms67-black-and-blue-catalog-en.pdf'],
        'MGI-MS67-BLU' => ['id' => 'catalogs/mgispec-ms67-black-and-blue-catalog-id.pdf', 'en' => 'catalogs/mgispec-ms67-black-and-blue-catalog-en.pdf'],
        'MGI-PC47-COF' => ['id' => 'catalogs/mgispec-pc47-yellow-and-coffee-catalog-id.pdf', 'en' => 'catalogs/mgispec-pc47-yellow-and-coffee-catalog-en.pdf'],
        'MGI-PC47-YEL' => ['id' => 'catalogs/mgispec-pc47-yellow-and-coffee-catalog-id.pdf', 'en' => 'catalogs/mgispec-pc47-yellow-and-coffee-catalog-en.pdf'],
        'MGI-SG013-LEO' => ['id' => 'catalogs/mgispec-sg013-leopard-print-catalog-id.pdf', 'en' => 'catalogs/mgispec-sg013-leopard-print-catalog-en.pdf'],
        'MGI-SG09-NVY' => ['id' => 'catalogs/mgispec-sg09-catalog-id.pdf', 'en' => 'catalogs/mgispec-sg09-catalog-en.pdf'],
        'MGI-SG09-COF' => ['id' => 'catalogs/mgispec-sg09-catalog-id.pdf', 'en' => 'catalogs/mgispec-sg09-catalog-en.pdf'],
        'MGI-SG63-BLK' => ['id' => 'catalogs/mgispec-sg63-black-catalog-id.pdf', 'en' => 'catalogs/mgispec-sg63-black-catalog-en.pdf'],
        'MGI-TT196-DGY' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT196-PCH' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT22-BLK' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT22-TEA' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT22-YEL' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT23-RED' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT26-BLK' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT26-COF' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT26-LEO' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-TT26-RED' => ['id' => 'catalogs/mgispec-titanium-series-catalog-id.pdf', 'en' => 'catalogs/mgispec-titanium-series-catalog-en.pdf'],
        'MGI-UT157-BLU' => ['id' => 'catalogs/mgispec-ultem-series-ut157-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut157-catalog-en.pdf'],
        'MGI-UT157-GRY' => ['id' => 'catalogs/mgispec-ultem-series-ut157-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut157-catalog-en.pdf'],
        'MGI-UT157-RED' => ['id' => 'catalogs/mgispec-ultem-series-ut157-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut157-catalog-en.pdf'],
        'MGI-UT157-TEA' => ['id' => 'catalogs/mgispec-ultem-series-ut157-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut157-catalog-en.pdf'],
        'MGI-UT158-BLU' => ['id' => 'catalogs/mgispec-ultem-series-ut158-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut158-catalog-en.pdf'],
        'MGI-UT158-GRY' => ['id' => 'catalogs/mgispec-ultem-series-ut158-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut158-catalog-en.pdf'],
        'MGI-UT158-RED' => ['id' => 'catalogs/mgispec-ultem-series-ut158-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut158-catalog-en.pdf'],
        'MGI-UT158-TEA' => ['id' => 'catalogs/mgispec-ultem-series-ut158-catalog-id.pdf', 'en' => 'catalogs/mgispec-ultem-series-ut158-catalog-en.pdf'],
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('products', 'catalog_pdf')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('catalog_pdf')->nullable();
            });
        }

        $disk = Storage::disk('public');
        $publicDir = public_path('storage');
        $mirror = is_dir($publicDir) && ! is_link($publicDir)
            && realpath($publicDir) !== realpath(storage_path('app/public'));

        $copied = [];
        foreach (self::CATALOGS as $sku => $files) {
            $row = DB::table('products')->where('sku', $sku)->first();
            if (! $row || filled($row->catalog_pdf)) {
                continue; // unknown product, or somebody already set catalogs in the CMS
            }

            $paths = [];
            foreach ($files as $code => $path) {
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
                $paths[$code] = $path;
            }

            if ($paths !== []) {
                DB::table('products')->where('id', $row->id)->update(['catalog_pdf' => json_encode($paths)]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'catalog_pdf')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('catalog_pdf');
            });
        }
    }
};
