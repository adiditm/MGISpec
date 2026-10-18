<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $branches = [
            [
                'country' => ['id' => 'Indonesia', 'en' => 'Indonesia'],
                'code' => 'id',
                'flag' => '🇮🇩',
                'role' => ['id' => 'Kantor Perwakilan & Layanan Pelanggan', 'en' => 'Representative Office & Customer Care'],
                'company' => 'PT. MEGA GLORYOUNG INTERNATIONAL',
                'city' => 'Jakarta Utara',
                'address' => 'Komplek Perkantoran CBD PLUIT Blok C No 8, Jl. Pluit Selatan Raya, RT.23/RW.8, Penjaringan, Kota Jakarta Utara, DKI Jakarta, Indonesia',
                'phone' => '+62 821-2881-5099',
                'whatsapp' => '6282128815099',
                'email' => 'mgiclubindonesia@gmail.com',
                'hours' => [
                    'id' => 'Senin – Jumat: 10.00 – 18.00 WIB | Sabtu: 10.00 – 14.00 WIB',
                    'en' => 'Monday – Friday: 10.00 – 18.00 WIB | Saturday: 10.00 – 14.00 WIB',
                ],
                'badge' => ['id' => 'Kantor Layanan Utama', 'en' => 'Main Service Hub'],
            ],
            [
                'country' => ['id' => 'Filipina', 'en' => 'Philippines'],
                'code' => 'ph',
                'flag' => '🇵🇭',
                'role' => ['id' => 'Kantor Pusat Mega Gloryoung International', 'en' => 'Mega Gloryoung International Headquarters'],
                'company' => 'Mega Gloryoung International HQ',
                'city' => 'Ortigas Center, Pasig City',
                'address' => 'Unit No. 2112, 21st floor, AIC Empire Tower, ADB Avenue, Ortigas Center, Pasig City, Metro Manila 1605, Philippines',
                'phone' => '+63 966 811 4229',
                'whatsapp' => '639668114229',
                'email' => 'mgihqphils@gmail.com',
                'hours' => [
                    'id' => 'Senin – Sabtu: 11.00 – 18.00',
                    'en' => 'Monday – Saturday: 11.00 AM – 06.00 PM',
                ],
                'badge' => ['id' => 'Headquarters', 'en' => 'Headquarters'],
            ],
            [
                'country' => ['id' => 'Malaysia', 'en' => 'Malaysia'],
                'code' => 'my',
                'flag' => '🇲🇾',
                'role' => ['id' => 'Kantor Perwakilan Malaysia', 'en' => 'Malaysia Representative Office'],
                'company' => 'MGICLUB (M) SDN. BHD.',
                'city' => 'Subang Jaya, Selangor',
                'address' => 'No 14-2, Jalan USJ 9/5P, UEP Subang Jaya, 47620 Selangor, Malaysia',
                'email' => 'MgiSuperMalaysia@gmail.com',
                'badge' => ['id' => 'Regional Office', 'en' => 'Regional Office'],
            ],
            [
                'country' => ['id' => 'Thailand', 'en' => 'Thailand'],
                'code' => 'th',
                'flag' => '🇹🇭',
                'role' => ['id' => 'Kantor Perwakilan Thailand', 'en' => 'Thailand Representative Office'],
                'company' => 'Mega Gloryoung International Co., LTD',
                'city' => 'Bangkok',
                'address' => '202 Le Concorde Tower, 10th floor, Room 1003, Ratchadapisek Road, Huai Khwang, Bangkok 10310, Thailand',
                'badge' => ['id' => 'Regional Office', 'en' => 'Regional Office'],
            ],
            [
                'country' => ['id' => 'MGIClub Global', 'en' => 'MGIClub Global'],
                'code' => 'global',
                'flag' => '🌐',
                'role' => ['id' => 'Pusat Keanggotaan Member & Kemitraan Internasional', 'en' => 'Global Member Portal & International Partnerships'],
                'city' => 'Global Network',
                'address' => 'MGIClub International Headquarters Network Support',
                'phone' => '+62 812-3456-7890',
                'whatsapp' => '6281234567890',
                'email' => 'support@mgiclub.com',
                'portal_url' => 'https://member.mgiclub.com',
                'hours' => [
                    'id' => '24/7 Portal Layanan Anggota Online',
                    'en' => '24/7 Online Member Support Portal',
                ],
                'badge' => ['id' => 'Official Portal', 'en' => 'Official Portal'],
            ],
        ];

        return view('contact', compact('branches'));
    }
}
