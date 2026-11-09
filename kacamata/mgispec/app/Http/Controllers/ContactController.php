<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $branches = [
            [
                'country' => ['id' => 'Indonesia', 'en' => 'Indonesia', 'cn' => '印度尼西亚'],
                'code' => 'id',
                'flag' => '🇮🇩',
                'company' => 'PT. MEGA GLORYOUNG INTERNATIONAL',
                'address' => 'Komplek Perkantoran CBD PLUIT Blok C No 8 Jl. Pluit Selatan Raya, RT23/RW.8, Penjaringan, Kota Jakarta Utara DKI Jakarta, Indonesia',
                'phone' => '+62 821-2881-5099',
                'whatsapp' => '6282128815099',
                // Indonesia has two hotlines (shown as separate lines and WhatsApp buttons)
                'contacts' => [
                    ['label' => ['id' => 'Customer Service', 'en' => 'Customer Service', 'cn' => '客户服务'], 'phone' => '+62 821-2881-5099', 'whatsapp' => '6282128815099'],
                    ['label' => ['id' => 'Hotline Tim Marketing', 'en' => 'Marketing Team Hotline', 'cn' => '营销团队热线'], 'phone' => '+62 823-1929-4294', 'whatsapp' => '6282319294294'],
                ],
                'email' => 'mgiclubindonesia@gmail.com',
                'hours' => [
                    'id' => ['Senin - Jumat 10.00 WIB - 18.00 WIB', 'Sabtu 10.00 WIB - 14.00 WIB'],
                    'en' => ['Monday - Friday 10.00 AM - 06.00 PM (WIB)', 'Saturday 10.00 AM - 02.00 PM (WIB)'],
                    'cn' => ['周一至周五 10:00 - 18:00（雅加达时间）', '周六 10:00 - 14:00（雅加达时间）'],
                ],
            ],
            [
                'country' => ['id' => 'Filipina', 'en' => 'Philippines', 'cn' => '菲律宾'],
                'code' => 'ph',
                'flag' => '🇵🇭',
                'company' => 'Mega Gloryoung International HQ',
                'address' => 'Unit No. 2112, 21st floor, AIC Empire Tower ADB Avenue, Ortigas Center, Pasig City, Metro Manila 1605, Philippines.',
                'phone' => '+63 966 811 4229',
                'whatsapp' => '639668114229',
                'email' => 'mgihqphils@gmail.com',
                'hours' => [
                    'id' => ['Senin - Sabtu 11.00 - 18.00'],
                    'en' => ['Monday to Saturday 11.00 AM - 06.00 PM'],
                    'cn' => ['周一至周六 11:00 - 18:00'],
                ],
            ],
            [
                'country' => ['id' => 'Malaysia', 'en' => 'Malaysia', 'cn' => '马来西亚'],
                'code' => 'my',
                'flag' => '🇲🇾',
                'company' => 'MGICLUB (M) SDN.BHD',
                'address' => 'No 14-2, Jalan USJ 9/5P, UEP Subang Jaya, 47620, Selangor.',
                'email' => 'MgiSuperMalaysia@gmail.com',
            ],
            [
                'country' => ['id' => 'Thailand', 'en' => 'Thailand', 'cn' => '泰国'],
                'code' => 'th',
                'flag' => '🇹🇭',
                'company' => 'Mega Gloryoung International Co., LTD',
                'address' => '202 Le Concorde Tower, 10th floor, Room 1003, Ratchadapisek Road, Huai Khwang, Bangkok 10310, Thailand',
            ],
        ];

        return view('contact', compact('branches'));
    }
}
