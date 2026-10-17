<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\PromoBanner;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@mgispec.com')],
            ['name' => 'Admin MGISpec', 'password' => env('ADMIN_PASSWORD', 'mgispec-admin-2026')],
        );

        $this->copyImages();
        $this->seedSliders();
        $this->seedPromos();
        $this->seedCatalog();

        Setting::put(['slider_effect' => 'fade', 'slider_delay' => 6]);
    }

    private function copyImages(): void
    {
        foreach (File::files(__DIR__.'/images') as $file) {
            Storage::disk('public')->put('seed/'.$file->getFilename(), $file->getContents());
        }
    }

    private function seedSliders(): void
    {
        Slider::query()->delete();

        $slides = [
            [
                'desktop_image' => [
                    'id' => 'sliders/slider-1-desktop-id.webp',
                    'en' => 'sliders/slider-1-desktop-en.webp',
                ],
                'mobile_image' => [
                    'id' => 'sliders/slider-1-mobile-id.webp',
                    'en' => 'sliders/slider-1-mobile-en.webp',
                ],
                'title' => [
                    'id' => 'Standar MGISpec',
                    'en' => 'The MGISpec Standard',
                ],
                'subtitle' => [
                    'id' => 'Generasi baru untuk kesehatan premium. Beyond vision, beyond protection.',
                    'en' => 'The next generation of premium wellness. Beyond vision, beyond protection.',
                ],
                'show_text' => false,
                'overlay' => 0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'desktop_image' => [
                    'id' => 'sliders/slider-2-desktop-id.webp',
                    'en' => 'sliders/slider-2-desktop-en.webp',
                ],
                'mobile_image' => [
                    'id' => 'sliders/slider-2-mobile-id.webp',
                    'en' => 'sliders/slider-2-mobile-en.webp',
                ],
                'title' => [
                    'id' => 'Dibangun Dari Ilmu',
                    'en' => 'Built On Science',
                ],
                'subtitle' => [
                    'id' => 'Dirancang dengan tujuan. Setiap detail memiliki arti.',
                    'en' => 'Designed with purpose. Every detail matters.',
                ],
                'show_text' => false,
                'overlay' => 0,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'desktop_image' => [
                    'id' => 'sliders/slider-3-desktop-id.webp',
                    'en' => 'sliders/slider-3-desktop-en.webp',
                ],
                'mobile_image' => [
                    'id' => 'sliders/slider-3-mobile-id.webp',
                    'en' => 'sliders/slider-3-mobile-en.webp',
                ],
                'title' => [
                    'id' => 'Kualitas Terverifikasi',
                    'en' => 'Quality You Can Verify',
                ],
                'subtitle' => [
                    'id' => 'Sertifikasi internasional terpercaya: US FDA, Halal, ISO, Great Eastern.',
                    'en' => 'Trusted international certifications: US FDA, Halal, ISO, Great Eastern.',
                ],
                'show_text' => false,
                'overlay' => 0,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'desktop_image' => [
                    'id' => 'sliders/slider-4-desktop-id.webp',
                    'en' => 'sliders/slider-4-desktop-en.webp',
                ],
                'mobile_image' => [
                    'id' => 'sliders/slider-4-mobile-id.webp',
                    'en' => 'sliders/slider-4-mobile-en.webp',
                ],
                'title' => [
                    'id' => 'Teknologi Bertemu Visi',
                    'en' => 'Where Technology Meets Vision',
                ],
                'subtitle' => [
                    'id' => 'Beyond vision. Beyond protection.',
                    'en' => 'Beyond vision. Beyond protection.',
                ],
                'show_text' => false,
                'overlay' => 0,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'desktop_image' => [
                    'id' => 'sliders/slider-5-desktop-id.webp',
                    'en' => 'sliders/slider-5-desktop-en.webp',
                ],
                'mobile_image' => [
                    'id' => 'sliders/slider-5-mobile-id.webp',
                    'en' => 'sliders/slider-5-mobile-en.webp',
                ],
                'title' => [
                    'id' => 'Perlindungan Mata Premium',
                    'en' => 'Advanced Eye Wellness',
                ],
                'subtitle' => [
                    'id' => 'Lindungi mata Anda hari ini. Rawat penglihatan Anda untuk masa depan.',
                    'en' => 'Protect your eyes today. Care for your vision for the future.',
                ],
                'show_text' => false,
                'overlay' => 0,
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            Slider::create($slide);
        }
    }

    private function seedPromos(): void
    {
        PromoBanner::query()->delete();

        $promos = [
            ['SiO₂', 'fa-solid fa-atom', 'Dioksida Silikon (SiO₂) Nano', 'Nano Silicon Dioxide (SiO₂)', 'Meningkatkan kejernihan optik, ketahanan terhadap goresan, dan daya rekat partikel pada bingkai.', 'Enhances optical clarity, scratch resistance, and nano-particle structural adhesion.'],
            ['TiO₂', 'fa-solid fa-sun', 'Dioksida Titanium (TiO₂)', 'Titanium Dioxide (TiO₂)', 'Perlindungan fotokatalitik alami, pemblokir radiasi UV tinggi, serta sifat antimikroba higienis.', 'Natural photocatalytic shielding, high-grade UV block, and hygienic antimicrobial properties.'],
            ['ZrO₂', 'fa-solid fa-gem', 'Dioksida Zirkonium (ZrO₂)', 'Zirconium Dioxide (ZrO₂)', 'Ketahanan termal tinggi, anti korosi keringat, serta memperkuat kekokohan fisik bingkai.', 'High thermal resistance, sweat corrosion defense, and reinforcement of frame toughness.'],
            ['GRAPHENE', 'fa-solid fa-circle-nodes', 'Grafena', 'Graphene', 'Material superkonduktif ultra-ringan, mendistribusikan panas merata dan anti-statis debu.', 'Ultra-lightweight superconductive matrix providing heat dissipation and anti-static dust repelling.'],
            ['AG NANO', 'fa-solid fa-shield-virus', 'Nano Perak', 'Nano Silver', 'Perlindungan antibakteri dan jamur berspektrum luas untuk menjaga kebersihan area kontak kulit.', 'Broad-spectrum antibacterial and antifungal protection for clean, hygienic skin contact.'],
            ['Ge', 'fa-solid fa-cubes', 'Germanium Organik', 'Organic Germanium', 'Membantu merangsang sirkulasi mikrovaskular dan meredakan kelelahan pada otot mata.', 'Assists in stimulating microvascular circulation and relieving ocular muscle fatigue.'],
            ['BAMBOO', 'fa-solid fa-leaf', 'Arang Bambu Aktif', 'Bamboo Charcoal', 'Penyerapan kelembapan optimal, menetralisir bau keringat, dan melepaskan ion alami.', 'Optimal moisture absorption, sweat odor neutralization, and natural ambient ion emission.'],
            ['FIR', 'fa-solid fa-fire-flame-curved', 'Mineral Far-Inframerah (FIR)', 'Far-Infrared (FIR) Mineral', 'Pancaran gelombang bio-inframerah jauh untuk menghangatkan dan melancarkan metabolisme mata.', 'Bio-far-infrared resonant emission that gently promotes ocular tissue metabolism and recovery.'],
            ['ANION', 'fa-solid fa-wind', 'Mineral Ion Negatif', 'Negative Ion Mineral', 'Melepaskan konsentrasi ion negatif alami untuk menyegarkan area mata seperti udara pegunungan.', 'Emits high concentration of natural negative ions to refresh eyes with mountain-pure micro-oxygen.'],
            ['ZnO', 'fa-solid fa-shield-halved', 'Oksida Seng (ZnO)', 'Zinc Oxide (ZnO)', 'Pelindung radiasi UV alami yang stabil, menenangkan kulit, dan mencegah iritasi.', 'Stable natural UV barrier that soothes sensitive skin around the eyes and prevents irritation.'],
        ];

        foreach ($promos as $i => [$badge, $icon, $titleId, $titleEn, $descId, $descEn]) {
            PromoBanner::create([
                'badge' => $badge,
                'icon' => $icon,
                'title' => ['id' => $titleId, 'en' => $titleEn],
                'description' => ['id' => $descId, 'en' => $descEn],
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function seedCatalog(): void
    {
        $this->call(CatalogProductSeeder::class);
    }
}

