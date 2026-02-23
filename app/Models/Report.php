<?php

namespace App\Models;

use App\Concerns\ManageBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @method static create(mixed $pull)
 * @method static find(mixed $decrypt)
 */
class Report extends Model
{
    use ManageBy;

    const PIHAK_PERKARA_VALUE = 'pihak_perkara';

    const PIHAK_PERKARA_LABEL = 'Pihak Perkara';

    const SAKSI_VALUE = 'saksi';

    const SAKSI_LABEL = 'Saksi';

    const TAMU_VALUE = 'tamu';

    const TAMU_LABEL = 'Tamu';

    const KUASA_HUKUM_VALUE = 'kuasa_hukum';

    const KUASA_HUKUM_LABEL = 'Kuasa Hukum';

    const NECESSARY_CHOICE = [
        self::PIHAK_PERKARA_VALUE => self::PIHAK_PERKARA_LABEL,
        self::SAKSI_VALUE => self::SAKSI_LABEL,
        self::TAMU_VALUE => self::TAMU_LABEL,
        self::KUASA_HUKUM_VALUE => self::KUASA_HUKUM_LABEL,
    ];

    protected $guarded = [];

    public static function getYears()
    {
        return self::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
    }

    public function getNecessaryAttribute($value)
    {
        return Str::title(str_replace('_', ' ', $value));
    }
}
