<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Chinese (cn) is the third website language. Fills a first-draft Chinese text for the content that
 * already exists in the CMS. It only fills EMPTY "cn" values (never overwrites anything an admin typed)
 * and only for texts it recognises, so edited or new content is left alone and simply falls back to English.
 * The wording is an AI draft and should be reviewed by a native speaker.
 */
return new class extends Migration
{
    private const FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function up(): void
    {
        $this->fillColumn('categories', 'name', [
            'MGISPEC Series' => 'MGISPEC 系列',
            'Optical Frame' => '光学镜架',
            'Titanium Series' => '钛金属系列',
            'Kids Series' => '儿童系列',
            'Sunglasses Series' => '太阳镜系列',
        ]);

        $this->fillColumn('promo_banners', 'title', [
            'Dioksida Silikon (SiO₂) Nano' => '纳米二氧化硅 (SiO₂)',
            'Dioksida Titanium (TiO₂)' => '二氧化钛 (TiO₂)',
            'Dioksida Zirkonium (ZrO₂)' => '二氧化锆 (ZrO₂)',
            'Grafena' => '石墨烯',
            'Nano Perak' => '纳米银',
            'Germanium Organik' => '有机锗',
            'Arang Bambu Aktif' => '活性竹炭',
            'Mineral Far-Inframerah (FIR)' => '远红外 (FIR) 矿物',
            'Mineral Ion Negatif' => '负离子矿物',
            'Oksida Seng (ZnO)' => '氧化锌 (ZnO)',
        ]);

        $this->fillColumn('promo_banners', 'description', [
            'Meningkatkan kejernihan optik, ketahanan terhadap goresan, dan daya rekat partikel pada bingkai.' => '提升光学清晰度和抗刮擦性能，并增强纳米颗粒在镜架上的附着力。',
            'Perlindungan fotokatalitik alami, pemblokir radiasi UV tinggi, serta sifat antimikroba higienis.' => '天然光催化防护，高效阻隔紫外线辐射，并具有卫生抗菌特性。',
            'Ketahanan termal tinggi, anti korosi keringat, serta memperkuat kekokohan fisik bingkai.' => '耐高温、抗汗液腐蚀，并增强镜架的整体坚固度。',
            'Material superkonduktif ultra-ringan, mendistribusikan panas merata dan anti-statis debu.' => '超轻导热材料，均匀散热并防止灰尘静电吸附。',
            'Perlindungan antibakteri dan jamur berspektrum luas untuk menjaga kebersihan area kontak kulit.' => '广谱抗菌防霉，保持与皮肤接触部位的清洁卫生。',
            'Membantu merangsang sirkulasi mikrovaskular dan meredakan kelelahan pada otot mata.' => '有助于促进微循环，缓解眼部肌肉疲劳。',
            'Penyerapan kelembapan optimal, menetralisir bau keringat, dan melepaskan ion alami.' => '出色的吸湿性能，中和汗味，并释放天然离子。',
            'Pancaran gelombang bio-inframerah jauh untuk menghangatkan dan melancarkan metabolisme mata.' => '释放生物远红外波，温暖并促进眼部新陈代谢。',
            'Melepaskan konsentrasi ion negatif alami untuk menyegarkan area mata seperti udara pegunungan.' => '释放高浓度天然负离子，让眼周如山间空气般清新。',
            'Pelindung radiasi UV alami yang stabil, menenangkan kulit, dan mencegah iritasi.' => '稳定的天然紫外线屏障，舒缓肌肤并防止刺激。',
        ]);

        $sliderTexts = [
            'Standar MGISpec' => 'MGISpec 标准',
            'Generasi baru untuk kesehatan premium. Beyond vision, beyond protection.' => '高端健康新一代。超越视觉，超越防护。',
            'Dibangun Dari Ilmu' => '以科学为基础',
            'Dirancang dengan tujuan. Setiap detail memiliki arti.' => '因目标而设计，每个细节都有意义。',
            'Kualitas Terverifikasi' => '品质可验证',
            'Sertifikasi internasional terpercaya: US FDA, Halal, ISO, Great Eastern.' => '值得信赖的国际认证：美国 FDA、清真、ISO、大东方。',
            'Teknologi Bertemu Visi' => '科技与视界的交汇',
            'Beyond vision. Beyond protection.' => '超越视觉，超越防护。',
            'Perlindungan Mata Premium' => '高端眼部呵护',
            'Lindungi mata Anda hari ini. Rawat penglihatan Anda untuk masa depan.' => '从今天起守护您的双眼，为未来呵护您的视力。',
        ];
        foreach (['title', 'subtitle', 'badge', 'cta_text', 'cta2_text'] as $column) {
            $this->fillColumn('sliders', $column, $sliderTexts);
        }

        $this->fillProducts();
        $this->fillFooterAbout();
    }

    public function down(): void
    {
        // Translations are content; nothing to undo.
    }

    private function fillProducts(): void
    {
        $series = [
            'Kids Series' => '儿童系列',
            'MGISPEC Series' => 'MGISPEC 系列',
            'Medical Safety Series' => '医用防护系列',
            'Photochromic Series' => '变色系列',
            'Ultem Optical Series' => 'Ultem 光学系列',
            'Titanium Series' => '钛金属系列',
            'Sunglasses Series' => '太阳镜系列',
        ];

        $highlights = [
            'Mengurangi mata lelah & tegang setelah berjam-jam menatap layar' => '减轻长时间盯着屏幕后的眼睛疲劳与紧张',
            'Bisa dipasang lensa korektif minus / plus / silinder' => '可配近视、远视及散光矫正镜片',
            'Gratis hardcase premium & microfiber cloth eksklusif MGISpec' => '赠送高级硬质眼镜盒及 MGISpec 专属超细纤维擦镜布',
        ];

        $specs = [
            'Lebar Lensa' => '镜片宽度',
            'Jarak Hidung (Bridge)' => '鼻梁宽度',
            'Panjang Tangkai' => '镜腿长度',
            'Material Frame' => '镜架材质',
            'Warna Frame' => '镜架颜色',
            'Berat' => '重量',
        ];

        $lens = [
            'Oxygen & Negative Ion Tech' => '氧气与负离子科技',
            'Memancarkan konsentrasi ion negatif alami yang membantu sirkulasi oksigen mikro di sekitar mata, meredakan ketegangan otot mata.' => '释放天然负离子，促进眼周微氧循环，缓解眼部肌肉紧张。',
            'Blue Light Defense' => '防蓝光护盾',
            'Menyaring radiasi sinar biru berbahaya (HEV 415–455 nm) dari layar ponsel pintar, tablet, laptop, dan pencahayaan LED.' => '过滤智能手机、平板、笔记本电脑和 LED 照明发出的有害蓝光 (HEV 415–455 nm)。',
            'UV400 Total Protection' => 'UV400 全面防护',
            'Memblokir 100% radiasi sinar UVA dan UVB hingga panjang gelombang 400 nm untuk pencegahan katarak dan kerusakan kornea dini.' => '100% 阻隔波长至 400 nm 的 UVA 和 UVB 紫外线，预防白内障和早期角膜损伤。',
            'Anti-Radiation Shield' => '防辐射护盾',
            'Lapisan pelindung multi-layer elektromagnetik untuk meminimalkan paparan gelombang radiasi elektromagnetik peralatan digital.' => '多层电磁防护镀膜，最大限度减少数码设备电磁辐射的影响。',
        ];

        foreach (DB::table('products')->get() as $product) {
            $update = [];

            $shortDescription = $this->decode($product->short_description);
            if ($shortDescription !== null && blank($shortDescription['cn'] ?? null)) {
                $name = (string) (($this->decode($product->name)['id'] ?? ''));
                $pattern = '/^Kacamata (.+?) '.preg_quote($name, '/').' berteknologi ion negatif & nano mineral untuk sirkulasi oksigen optimal di sekitar area mata\.$/u';
                if ($name !== '' && preg_match($pattern, (string) ($shortDescription['id'] ?? ''), $m) && isset($series[$m[1]])) {
                    $shortDescription['cn'] = "{$series[$m[1]]} {$name}，采用负离子与纳米矿物科技，促进眼周氧气循环，呵护眼部健康。";
                    $update['short_description'] = $this->encode($shortDescription);
                }
            }

            $description = $this->decode($product->description);
            if ($description !== null && blank($description['cn'] ?? null)) {
                $skeleton = '<p>Menghabiskan waktu berjam-jam di depan layar monitor atau aktivitas harian dapat memicu kelelahan mata, mata kering, serta ketegangan visual. <strong>(.+?)</strong> hadir dengan formulasi teknologi ion negatif dan perlindungan anti radiasi modern untuk menjaga mata Anda tetap segar dan terlindungi sepanjang hari.</p><p>Dirancang dengan frame ergonomis berbahan <em>(.+?)</em> yang kuat, fleksibel, serta sangat ringan, kacamata ini tidak menimbulkan tekanan pada hidung dan pelipis telinga, sangat nyaman digunakan untuk bekerja, belajar, hingga aktivitas luar ruangan.</p>';
                if (preg_match('#^'.$skeleton.'$#us', (string) ($description['id'] ?? ''), $m)) {
                    $description['cn'] = "<p>长时间面对电脑屏幕或日常活动，容易引发眼睛疲劳、干涩和视觉紧张。<strong>{$m[1]}</strong> 采用先进的负离子技术和现代防辐射保护配方，让您的双眼全天保持清爽与安全。</p><p>镜架采用符合人体工学的 <em>{$m[2]}</em> 材质，坚固、柔韧且极其轻盈，不会对鼻梁和太阳穴造成压迫，非常适合工作、学习和户外活动。</p>";
                    $update['description'] = $this->encode($description);
                }
            }

            $items = $this->decode($product->highlights);
            if ($items !== null) {
                $changed = false;
                foreach ($items as &$item) {
                    $text = &$item['text'];
                    if (! is_array($text) || filled($text['cn'] ?? null)) {
                        continue;
                    }
                    $id = (string) ($text['id'] ?? '');
                    if (isset($highlights[$id])) {
                        $text['cn'] = $highlights[$id];
                        $changed = true;
                    } elseif (preg_match('/^Frame ergonomis berbahan (.+), ringan & nyaman seharian$/u', $id, $m)) {
                        $text['cn'] = "采用 {$m[1]} 制成的人体工学镜架，轻盈舒适，全天佩戴无负担";
                        $changed = true;
                    }
                }
                unset($item, $text);
                if ($changed) {
                    $update['highlights'] = $this->encode($items);
                }
            }

            $items = $this->decode($product->specs);
            if ($items !== null && $this->fillItems($items, ['label'], $specs)) {
                $update['specs'] = $this->encode($items);
            }

            $items = $this->decode($product->lens_technologies ?? null);
            if ($items !== null && $this->fillItems($items, ['title', 'description'], $lens)) {
                $update['lens_technologies'] = $this->encode($items);
            }

            if ($update) {
                DB::table('products')->where('id', $product->id)->update($update);
            }
        }
    }

    private function fillFooterAbout(): void
    {
        $row = DB::table('settings')->where('key', 'footer_about')->first();
        $value = $row ? $this->decode($row->value) : null;

        if ($value === null || filled($value['cn'] ?? null) || blank($value['id'] ?? null)) {
            return;
        }

        if (! str_starts_with((string) $value['id'], 'MGISPEC menggabungkan rekayasa optik canggih')) {
            return;
        }

        $value['cn'] = 'MGISPEC 将先进的光学工程与源自自然的创新功能材料相结合，带来出色的视觉舒适度与性能。每一副 MGISPEC 镜架均采用先进的纳米复合技术制造，并融入独家功能材料。这种创新结构提升了耐用性、舒适度与稳定性，专为日常佩戴的最佳表现而设计。';

        DB::table('settings')->where('key', 'footer_about')->update(['value' => $this->encode($value)]);
    }

    /** Fill "cn" in a JSON column of {id, en, cn} objects, matching on the Indonesian text. */
    private function fillColumn(string $table, string $column, array $map): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        foreach (DB::table($table)->select('id', $column)->get() as $row) {
            $value = $this->decode($row->$column);
            $id = is_array($value) ? ($value['id'] ?? null) : null;

            if ($id === null || filled($value['cn'] ?? null) || ! isset($map[$id])) {
                continue;
            }

            $value['cn'] = $map[$id];
            DB::table($table)->where('id', $row->id)->update([$column => $this->encode($value)]);
        }
    }

    /** Fill "cn" on the given sub-keys of each repeater item; true when anything changed. */
    private function fillItems(array &$items, array $keys, array $map): bool
    {
        $changed = false;

        foreach ($items as &$item) {
            foreach ($keys as $key) {
                $text = $item[$key] ?? null;
                if (! is_array($text) || filled($text['cn'] ?? null) || ! isset($map[$text['id'] ?? ''])) {
                    continue;
                }
                $item[$key]['cn'] = $map[$text['id']];
                $changed = true;
            }
        }

        return $changed;
    }

    private function decode(mixed $json): ?array
    {
        $value = is_string($json) ? json_decode($json, true) : null;

        return is_array($value) ? $value : null;
    }

    private function encode(array $value): string
    {
        return json_encode($value, self::FLAGS);
    }
};
