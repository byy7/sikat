<?php

namespace App\Helpers;

class CustomBadgeHelper
{
    public static function badgeNecessary(string $necessary)
    {
        switch ($necessary) {
            case 'Pihak Perkara':
                $color = 'red';
                break;
            case 'Saksi':
                $color = 'blue';
                break;
            case 'Tamu':
                $color = 'yellow';
                break;
            case 'Kuasa Hukum':
                $color = 'teal';
                break;
        }

        return $color;
    }
}
