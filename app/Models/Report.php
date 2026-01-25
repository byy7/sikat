<?php

namespace App\Models;

use App\Concerns\ManageBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Report extends Model
{
    use ManageBy;

    protected $guarded = ['id'];

    public function scopeGetTotalData(Builder $query, string $type, $month = null, $year = null)
    {
        if (is_null($month) && is_null($year)) {
            $data = $query->where('necessary', $type)->count();
        } else {
            $data = $query->where('necessary', $type)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();
        }

        return $data;
    }

    public function getNecessaryAttribute($value)
    {
        return Str::title(str_replace('_', ' ', $value));
    }
}
