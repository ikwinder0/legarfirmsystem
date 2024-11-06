<?php

namespace App\Rules;

use App\Models\SalePurchaseAgreement;
use Illuminate\Contracts\Validation\Rule;

class SalePurchaseRangePresent implements Rule
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
        $sale_purchase_ranges = SalePurchaseAgreement::where('id','!=', request('id'));
        $min = $sale_purchase_ranges->min('min_price');
        $max = $sale_purchase_ranges->max('max_price');
        return !($min <= (float)$value && (float)$value <= $max);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The range is already taken.';
    }
}
