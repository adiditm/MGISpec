<?php

namespace App\Support;

/** Indonesia's two WhatsApp hotlines: shown on the contact page and offered in the WhatsApp chooser popup. */
class WhatsAppContacts
{
    public static function indonesia(): array
    {
        return [
            ['label' => ['id' => 'Customer Service', 'en' => 'Customer Service', 'cn' => '客户服务'], 'phone' => '+62 821-2881-5099', 'whatsapp' => '6282128815099'],
            ['label' => ['id' => 'Hotline Tim Marketing', 'en' => 'Marketing Team Hotline', 'cn' => '营销团队热线'], 'phone' => '+62 823-1929-4294', 'whatsapp' => '6282319294294'],
        ];
    }
}
