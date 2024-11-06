<?php
/**
 * Class CasePointService
 * @author ningmar
 * @package App\Services
 */

namespace App\Services;


use App\Models\CaseSizePointSetting;

class CasePointService
{
    public function findCasePointByPrice($price) {
        return CaseSizePointSetting::where([
            ['min_price', '<=', $price],
            ['max_price', '>=', $price]
        ])->first();
    }
}