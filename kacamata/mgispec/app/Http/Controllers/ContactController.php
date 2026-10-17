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
                'city' => 'Jakarta Barat',
                'address' => 'Rukan Sentra Niaga Blok T1 No. 12-14, Puri Indah, Kembangan, Jakarta Barat 11610, Indonesia',
                'phone' => '+62 812-3456-7890',
                'whatsapp' => '6281234567890',
                'email' => 'indonesia@mgispec.com',
                'hours' => [
                    'id' => 'Senin – Jumat: 09:00 – 17:00 WIB | Sabtu: 09:00 – 14:00 WIB',
                    'en' => 'Monday – Friday: 09:00 – 17:00 WIB | Saturday: 09:00 – 14:00 WIB',
                ],
                'badge' => ['id' => 'Kantor Layanan Utama', 'en' => 'Main Service Hub'],
            ],
            [
                'country' => ['id' => 'Filipina', 'en' => 'Philippines'],
                'code' => 'ph',
                'flag' => '🇵🇭',
                'role' => ['id' => 'Kantor Perwakilan Regional Manila', 'en' => 'Manila Regional Representative Office'],
                'city' => 'Bonifacio Global City, Taguig',
                'address' => 'Unit 1204, Trade & Financial Tower, 7th Ave cor. 32nd St., Bonifacio Global City (BGC), Taguig, Metro Manila, Philippines',
                'phone' => '+63 917-123-4567',
                'whatsapp' => '639171234567',
                'email' => 'philippines@mgispec.com',
                'hours' => [
                    'id' => 'Senin – Jumat: 09:00 – 18:00 PHT',
                    'en' => 'Monday – Friday: 09:00 – 18:00 PHT',
                ],
                'badge' => ['id' => 'Regional Office', 'en' => 'Regional Office'],
            ],
            [
                'country' => ['id' => 'Malaysia', 'en' => 'Malaysia'],
                'code' => 'my',
                'flag' => '🇲🇾',
                'role' => ['id' => 'Perwakilan Lisensi Medis & Distribusi', 'en' => 'Medical Device Licensing & Distribution'],
                'city' => 'Kuala Lumpur',
                'address' => 'Suite 8-2, Level 8, Menara Kencana, Solaris Dutamas, 50480 Kuala Lumpur, Malaysia',
                'phone' => '+60 12-345-6789',
                'whatsapp' => '60123456789',
                'email' => 'malaysia@mgispec.com',
                'hours' => [
                    'id' => 'Senin – Jumat: 09:00 – 17:30 MYT',
                    'en' => 'Monday – Friday: 09:00 – 17:30 MYT',
                ],
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
