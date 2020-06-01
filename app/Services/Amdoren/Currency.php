<?php

namespace App\Services\Amdoren;

use Illuminate\Support\Collection;

class Currency
{
    public static function list(): Collection
    {
        $list = collect(config('amdoren.currencies'));

        return $list;
    }
}
