<?php

namespace App\Rules;

use App\Models\CaseSizePointSetting;
use Illuminate\Contracts\Validation\Rule;

class CaseSizePricePresent implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $casePoint = new CaseSizePointSetting();
        if(request('id'))
            $casePoint = $casePoint->where('id','!=', request('id'));
        $min = $casePoint->min('min_price');
        $max = $casePoint->max('max_price');
        return !($min <= (float)$value && (float)$value <= $max);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The price is already taken';
    }
}
