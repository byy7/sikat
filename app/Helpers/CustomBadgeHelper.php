<?php

namespace App\Helpers;

class CustomBadgeHelper
{
    public static function badgeNecessary(string $necessary)
    {
        switch ($necessary) {
            case 'Pihak Perkara':
                $color = 'emerald';
                break;
            case 'Saksi':
                $color = 'blue';
                break;
            case 'Tamu':
                $color = 'red';
                break;
            case 'Kuasa Hukum':
                $color = 'yellow';
                break;
        }

        return $color;
    }
}
