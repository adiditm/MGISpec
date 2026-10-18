<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** SKU => [old name (id), old name (en), new name taken from the photo folder]. */
    private const NAMES = [
        'MGI-KID13-PNK' => ['MGISpec Kids Care KID13 - Pink', 'MGISpec Kids Care KID13 - Pink', 'MGISPEC KID13 - Pink'],
        'MGI-KID29-BLU' => ['MGISpec Kids Care KID29 - Biru', 'MGISpec Kids Care KID29 - Blue', 'MGISPEC KID29 - Blue'],
        'MGI-MS131-BLK' => ['MGISpec Hospital Grade MS131 - Hitam', 'MGISpec Hospital Grade MS131 - Black', 'MGISPEC MS131 - Black'],
        'MGI-MS131-BRN' => ['MGISpec Hospital Grade MS131 - Cokelat', 'MGISpec Hospital Grade MS131 - Brown', 'MGISPEC MS131 - Brown'],
        'MGI-MS138B-RED' => ['MGISpec Hospital Grade MS138B - Merah', 'MGISpec Hospital Grade MS138B - Red', 'MGISPEC MS138B - Red'],
        'MGI-MS14-GRY' => ['MGISpec Hospital Grade MS14 - Abu-abu', 'MGISpec Hospital Grade MS14 - Grey', 'MGISPEC MS14 - Grey'],
        'MGI-MS161-COF' => ['MGISpec Hospital Grade MS161 - Kopi', 'MGISpec Hospital Grade MS161 - Coffee', 'MGISPEC MS161 - Coffee'],
        'MGI-MS191-LEO' => ['MGISpec Hospital Grade MS191 - Leopard Print', 'MGISpec Hospital Grade MS191 - Leopard Print', 'MGISPEC MS191 - Leopard Print'],
        'MGI-MS28-BLK' => ['MGISpec Hospital Grade MS28 - Hitam', 'MGISpec Hospital Grade MS28 - Black', 'MGISPEC MS28 - Black'],
        'MGI-MS28-TEA' => ['MGISpec Hospital Grade MS28 - Tea Amber', 'MGISpec Hospital Grade MS28 - Tea Amber', 'MGISPEC MS28 - Tea'],
        'MGI-MS35-BLK' => ['MGISpec Hospital Grade MS35 - Hitam', 'MGISpec Hospital Grade MS35 - Black', 'MGISPEC MS35 - Black'],
        'MGI-MS35-RED' => ['MGISpec Hospital Grade MS35 - Merah', 'MGISpec Hospital Grade MS35 - Red', 'MGISPEC MS35 - Red'],
        'MGI-MS41-BLK' => ['MGISpec Hospital Grade MS41 - Hitam Doff', 'MGISpec Hospital Grade MS41 - Matte Black', 'MGISPEC MS41 - Black'],
        'MGI-MS41-GLS' => ['MGISpec Hospital Grade MS41 - Hitam Kilap', 'MGISpec Hospital Grade MS41 - Gloss Black', 'MGISPEC MS41 - Gloss Black'],
        'MGI-MS44-BLK' => ['MGISpec Hospital Grade MS44 - Hitam', 'MGISpec Hospital Grade MS44 - Black', 'MGISPEC MS44 - Black'],
        'MGI-MS44-GRN' => ['MGISpec Hospital Grade MS44 - Hijau', 'MGISpec Hospital Grade MS44 - Green', 'MGISPEC MS44 - Green'],
        'MGI-MS45-GRN' => ['MGISpec Hospital Grade MS45 - Hijau', 'MGISpec Hospital Grade MS45 - Green', 'MGISPEC MS45 - Green'],
        'MGI-MS45-RED' => ['MGISpec Hospital Grade MS45 - Merah', 'MGISpec Hospital Grade MS45 - Red', 'MGISPEC MS45 - Red'],
        'MGI-MS610-WHT' => ['MGISpec Hospital Grade MS610 - Putih', 'MGISpec Hospital Grade MS610 - White', 'MGISPEC MS610 - White'],
        'MGI-MS67-BLK' => ['MGISpec Hospital Grade MS67 - Hitam', 'MGISpec Hospital Grade MS67 - Black', 'MGISPEC MS67 - Black'],
        'MGI-MS67-BLU' => ['MGISpec Hospital Grade MS67 - Biru', 'MGISpec Hospital Grade MS67 - Blue', 'MGISPEC MS67 - Blue'],
        'MGI-TT196-DGY' => ['MGISpec Pure Titanium TT196 - Abu-abu Gelap', 'MGISpec Pure Titanium TT196 - Dark Grey', 'MGISPEC TITANIUM - TT196 - Dark Grey'],
        'MGI-TT196-PCH' => ['MGISpec Pure Titanium TT196 - Peach Rose', 'MGISpec Pure Titanium TT196 - Peach Rose', 'MGISPEC TITANIUM - TT196 - Peach'],
        'MGI-TT22-BLK' => ['MGISpec Pure Titanium TT22 - Hitam', 'MGISpec Pure Titanium TT22 - Black', 'MGISPEC TITANIUM - TT22 - Black'],
        'MGI-TT22-TEA' => ['MGISpec Pure Titanium TT22 - Tea Amber', 'MGISpec Pure Titanium TT22 - Tea Amber', 'MGISPEC TITANIUM - TT22 - Tea'],
        'MGI-TT22-YEL' => ['MGISpec Pure Titanium TT22 - Kuning Emas', 'MGISpec Pure Titanium TT22 - Yellow Gold', 'MGISPEC TITANIUM - TT22 - Yellow'],
        'MGI-TT23-RED' => ['MGISpec Pure Titanium TT23 - Merah Crimson', 'MGISpec Pure Titanium TT23 - Crimson Red', 'MGISPEC TITANIUM - TT23 - Red'],
        'MGI-TT26-BLK' => ['MGISpec Pure Titanium TT26 - Hitam', 'MGISpec Pure Titanium TT26 - Black', 'MGISPEC TITANIUM - TT26 - Black'],
        'MGI-TT26-COF' => ['MGISpec Pure Titanium TT26 - Kopi', 'MGISpec Pure Titanium TT26 - Coffee', 'MGISPEC TITANIUM - TT26 - Coffee'],
        'MGI-TT26-LEO' => ['MGISpec Pure Titanium TT26 - Leopard Print', 'MGISpec Pure Titanium TT26 - Leopard Print', 'MGISPEC TITANIUM - TT26 - Leopard Print'],
        'MGI-TT26-RED' => ['MGISpec Pure Titanium TT26 - Merah', 'MGISpec Pure Titanium TT26 - Red', 'MGISPEC TITANIUM - TT26 - Red'],
        'MGI-SG013-LEO' => ['MGISpec Polarized Sunglass SG013 - Leopard Print', 'MGISpec Polarized Sunglass SG013 - Leopard Print', 'MGISPEC SUNGLASSES - SG013 - Leopard Print'],
        'MGI-SG09-COF' => ['MGISpec Polarized Sunglass SG09 - Cokelat Kopi', 'MGISpec Polarized Sunglass SG09 - Coffee Brown', 'MGISPEC SUNGLASSES - SG09 - Coffee Brown'],
        'MGI-SG09-NVY' => ['MGISpec Polarized Sunglass SG09 - Biru Dongker', 'MGISpec Polarized Sunglass SG09 - Navy Blue', 'MGISPEC SUNGLASSES - SG09 - Navy Blue'],
        'MGI-SG63-BLK' => ['MGISpec Polarized Sunglass SG63 - Hitam Optik', 'MGISpec Polarized Sunglass SG63 - Black Optical', 'MGISPEC SUNGLASSES - SG63 - Black Optical'],
    ];

    private const BADGES = [
        'Hospital Grade' => 'MGISPEC Series',
        'Pure Titanium' => 'Titanium Series',
        'Kids Care' => 'Kids Series',
        'Polarized UV400' => 'Sunglasses Series',
    ];

    /** Series phrases used in the seeded descriptions, longest first. */
    private const PHRASES = [
        'Hospital Grade Medical Series' => 'MGISPEC Series',
        'Pure Titanium Premium Series' => 'Titanium Series',
        'Polarized Sunglass Series' => 'Sunglasses Series',
        'Kacamata Anak & Remaja' => 'Kids Series',
        'Kids & Teens Eyewear' => 'Kids Series',
        'Hospital Grade ' => '',
        'Hospital Grade' => '',
    ];

    private const CATEGORIES = [
        'medical' => 'MGISPEC Series',
        'premium' => 'Titanium Series',
        'kids' => 'Kids Series',
        'sunglass' => 'Sunglasses Series',
    ];

    /**
     * Owner feedback 18-10-2026: drop "Hospital Grade" wording, name products after their photo folder,
     * rename the series, and replace the footer profile text and Facebook link.
     */
    public function up(): void
    {
        foreach (DB::table('products')->get() as $product) {
            $names = self::NAMES[$product->sku] ?? null;
            $replace = self::PHRASES;
            if ($names) {
                $replace = [$names[0] => $names[2], $names[1] => $names[2]] + $replace;
            }

            $update = [];
            foreach (['short_description', 'description', 'highlights', 'specs', 'technologies'] as $column) {
                if ($product->$column !== null) {
                    $update[$column] = json_encode($this->replaceIn(json_decode($product->$column, true), $replace), JSON_UNESCAPED_UNICODE);
                }
            }
            if ($names) {
                $name = json_decode($product->name, true) ?: [];
                $update['name'] = json_encode(array_map(fn () => $names[2], $name ?: ['id' => '', 'en' => '']), JSON_UNESCAPED_UNICODE);
            }
            if (isset(self::BADGES[$product->badge])) {
                $update['badge'] = self::BADGES[$product->badge];
            }

            DB::table('products')->where('id', $product->id)->update($update);
        }

        foreach (self::CATEGORIES as $slug => $name) {
            DB::table('categories')->where('slug', $slug)->update(['name' => json_encode(['id' => $name, 'en' => $name])]);
        }

        Setting::put([
            'footer_about' => Setting::DEFAULTS['footer_about'],
            'facebook' => Setting::DEFAULTS['facebook'],
        ]);
    }

    public function down(): void
    {
        // Content update; nothing to undo.
    }

    private function replaceIn(mixed $value, array $replace): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->replaceIn($v, $replace), $value);
        }

        return is_string($value) ? strtr($value, $replace) : $value;
    }
};
