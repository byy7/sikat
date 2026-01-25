<?php

namespace App\Helpers;

class CustomBadgeHelper
{
    public static function badgeNecessary(string $necessary)
    {
        switch ($necessary) {
            case 'Pihak Perkara':
                $color = 'bg-[#FF6B6B]';
                break;
            case 'Saksi':
                $color = 'bg-[#4D96FF]';
                break;
            case 'Tamu':
                $color = 'bg-[#FFC75F]';
                break;
            case 'Kuasa Hukum':
                $color = 'bg-[#6BCF9D]';
                break;
        }

        return $color;
    }
}
