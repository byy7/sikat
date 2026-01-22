<?php

namespace App\Consts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Month
{
    public static function getFullMonth(): Collection
    {
        Carbon::setLocale('id');

        return collect(range(1, 12))->map(function ($month) {
            return Carbon::create()->month($month)->translatedFormat('F');
        });
    }
}
