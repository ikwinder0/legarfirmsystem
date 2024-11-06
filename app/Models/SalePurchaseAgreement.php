<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SalePurchaseAgreement extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'sale_purchase_agreements';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function addAmount()
    {
        $agreement_ranges = SalePurchaseAgreement::orderBy('min_price')->get()->toArray();

        $amounts = [];
        for ($i=0; $i < sizeof($agreement_ranges); $i++) {
            $amount = 0.0;
            for ($j=$i-1; $j >= 0; $j--) {
                $amount += ($agreement_ranges[$j]['max_price']-$agreement_ranges[$j]['min_price']+1) * ($agreement_ranges[$j]['fees_rate']/100);
            }
            $amounts[$agreement_ranges[$i]['id']] = $amount;
        }

        return $amounts[$this->id];
    }

    public static function getRangeFromAmount($amount)
    {
        return SalePurchaseAgreement::firstWhere([
            ['min_price', '<', $amount],
            ['max_price', '>=', $amount]
        ]);
    }

    public static function calculateTotalFees($amount)
    {
        $spa = SalePurchaseAgreement::getRangeFromAmount($amount);
        $addAmount = $spa->addAmount();
        return (($amount - ($spa->min_price - 1.0)) * ($spa->fees_rate/100)) + $addAmount;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
