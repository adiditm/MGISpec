<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CatalogProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'medical' => Category::firstOrCreate(['slug' => 'medical'], ['name' => ['id' => 'Medical Series', 'en' => 'Medical Series']]),
            'optical' => Category::firstOrCreate(['slug' => 'optical'], ['name' => ['id' => 'Optical Frame', 'en' => 'Optical Frame']]),
            'premium' => Category::firstOrCreate(['slug' => 'premium'], ['name' => ['id' => 'Premium Titanium', 'en' => 'Premium Titanium']]),
            'kids' => Category::firstOrCreate(['slug' => 'kids'], ['name' => ['id' => 'Anak & Remaja', 'en' => 'Kids & Teens']]),
            'sunglass' => Category::firstOrCreate(['slug' => 'sunglass'], ['name' => ['id' => 'Sunglass', 'en' => 'Sunglass']]),
        ];

        $jsonFile = __DIR__.'/products_catalog_data.json';
        if (! File::exists($jsonFile)) {
            $this->command?->error("Manifest file {$jsonFile} does not exist.");
            return;
        }

        $items = json_decode(File::get($jsonFile), true);

        // Remove old sample seed products if they exist
        Product::where('sku', 'like', 'MGI-%')
            ->whereNotIn('sku', array_column($items, 'sku'))
            ->delete();

        foreach ($items as $item) {
            $cat = $categories[$item['cat']] ?? null;
            if (! $cat) {
                continue;
            }

            $nameId = $item['name_id'];
            $nameEn = $item['name_en'];
            $seriesId = $item['series_id'] ?? 'Kacamata MGISpec';
            $seriesEn = $item['series_en'] ?? 'MGISpec Eyewear';
            $materialId = $item['material'];
            $materialEn = $item['material_en'];
            $colorId = $item['color_id'];
            $colorEn = $item['color_en'];

            $shortDesc = [
                'id' => "Kacamata {$seriesId} {$nameId} berteknologi ion negatif & nano mineral untuk sirkulasi oksigen optimal di sekitar area mata.",
                'en' => "{$seriesEn} {$nameEn} featuring negative ion & nano-mineral technology for optimal oxygen circulation and eye wellness.",
            ];

            $description = [
                'id' => "<p>Menghabiskan waktu berjam-jam di depan layar monitor atau aktivitas harian dapat memicu kelelahan mata, mata kering, serta ketegangan visual. <strong>{$nameId}</strong> hadir dengan formulasi teknologi ion negatif dan perlindungan anti radiasi modern untuk menjaga mata Anda tetap segar dan terlindungi sepanjang hari.</p><p>Dirancang dengan frame ergonomis berbahan <em>{$materialId}</em> yang kuat, fleksibel, serta sangat ringan, kacamata ini tidak menimbulkan tekanan pada hidung dan pelipis telinga, sangat nyaman digunakan untuk bekerja, belajar, hingga aktivitas luar ruangan.</p>",
                'en' => "<p>Prolonged screen exposure and everyday activities often lead to eye fatigue, dryness, and visual tension. <strong>{$nameEn}</strong> is engineered with advanced negative ion technology and modern radiation protection to keep your eyes refreshed and safeguarded throughout the day.</p><p>Crafted with an ergonomic frame using <em>{$materialEn}</em> that is durable, flexible, and exceptionally lightweight, ensuring zero uncomfortable pressure on your nose bridge or temples — perfect for work, study, and daily active use.</p>",
            ];

            $highlights = [
                ['text' => ['id' => 'Mengurangi mata lelah & tegang setelah berjam-jam menatap layar', 'en' => 'Reduces eye fatigue & strain from prolonged screen use']],
                ['text' => ['id' => "Frame ergonomis berbahan {$materialId}, ringan & nyaman seharian", 'en' => "Ergonomic frame made of {$materialEn}, lightweight & comfortable for all-day wear"]],
                ['text' => ['id' => 'Bisa dipasang lensa korektif minus / plus / silinder', 'en' => 'Compatible with prescription corrective lenses (minus / plus / astigmatism)']],
                ['text' => ['id' => 'Gratis hardcase premium & microfiber cloth eksklusif MGISpec', 'en' => 'Complimentary premium hardcase & exclusive MGISpec microfiber cloth']],
            ];

            $specs = [
                ['label' => ['id' => 'Lebar Lensa', 'en' => 'Lens Width'], 'value' => $item['lens']],
                ['label' => ['id' => 'Jarak Hidung (Bridge)', 'en' => 'Bridge'], 'value' => $item['bridge']],
                ['label' => ['id' => 'Panjang Tangkai', 'en' => 'Temple Length'], 'value' => $item['temple']],
                ['label' => ['id' => 'Material Frame', 'en' => 'Frame Material'], 'value' => $materialId],
                ['label' => ['id' => 'Warna Frame', 'en' => 'Frame Color'], 'value' => $colorId],
                ['label' => ['id' => 'Berat', 'en' => 'Weight'], 'value' => $item['weight']],
            ];

            $technologies = [
                [
                    'icon' => 'fa-solid fa-wind',
                    'title' => ['id' => 'Oxygen & Negative Ion Tech', 'en' => 'Oxygen & Negative Ion Tech'],
                    'description' => [
                        'id' => 'Memancarkan konsentrasi ion negatif alami yang membantu sirkulasi oksigen mikro di sekitar mata, meredakan ketegangan otot mata.',
                        'en' => 'Emits natural negative ions promoting micro-oxygen circulation around the eyes, relaxing strained ocular muscles.',
                    ],
                ],
                [
                    'icon' => 'fa-solid fa-display',
                    'title' => ['id' => 'Blue Light Defense', 'en' => 'Blue Light Defense'],
                    'description' => [
                        'id' => 'Menyaring radiasi sinar biru berbahaya (HEV 415–455 nm) dari layar ponsel pintar, tablet, laptop, dan pencahayaan LED.',
                        'en' => 'Filters harmful high-energy visible blue light (HEV 415–455 nm) emitted by smartphones, laptops, and LED lighting.',
                    ],
                ],
                [
                    'icon' => 'fa-solid fa-sun',
                    'title' => ['id' => 'UV400 Total Protection', 'en' => 'UV400 Total Protection'],
                    'description' => [
                        'id' => 'Memblokir 100% radiasi sinar UVA dan UVB hingga panjang gelombang 400 nm untuk pencegahan katarak dan kerusakan kornea dini.',
                        'en' => 'Blocks 100% of UVA & UVB radiation up to 400 nm wavelength, protecting against premature corneal damage and cataracts.',
                    ],
                ],
                [
                    'icon' => 'fa-solid fa-shield-halved',
                    'title' => ['id' => 'Anti-Radiation Shield', 'en' => 'Anti-Radiation Shield'],
                    'description' => [
                        'id' => 'Lapisan pelindung multi-layer elektromagnetik untuk meminimalkan paparan gelombang radiasi elektromagnetik peralatan digital.',
                        'en' => 'Multi-layer electromagnetic shield coating minimizing radiation exposure from daily digital equipment.',
                    ],
                ],
            ];

            Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'category_id' => $cat->id,
                    'slug' => $item['slug'],
                    'name' => ['id' => $nameId, 'en' => $nameEn],
                    'badge' => $item['badge'],
                    'badge_color' => $item['badge_color'],
                    'thumbnail' => $item['thumbnail'],
                    'gallery' => $item['gallery'],
                    'short_description' => $shortDesc,
                    'description' => $description,
                    'highlights' => $highlights,
                    'specs' => $specs,
                    'technologies' => $technologies,
                    'is_featured' => true,
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}
