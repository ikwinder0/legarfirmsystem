<?php

namespace App\Rules;

use App\Models\TransferMemo;
use Illuminate\Contracts\Validation\Rule;

class TransferMemoRangePresent implements Rule
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
        $transfer_memos = TransferMemo::where('id','!=', request('id'));
        $min = $transfer_memos->min('min_price');
        $max = $transfer_memos->max('max_price');
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
