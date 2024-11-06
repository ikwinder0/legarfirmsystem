<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class TransferMemoStampDuty extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'transfer_memo_stamp_duties';
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
        $tm_ranges = TransferMemoStampDuty::orderBy('min_price')->get()->toArray();

        $amounts = [];
        for ($i=0; $i < sizeof($tm_ranges); $i++) {
            $amount = 0.0;
            for ($j=$i-1; $j >= 0; $j--) {
                $amount += ($tm_ranges[$j]['max_price']-$tm_ranges[$j]['min_price']+1) * ($tm_ranges[$j]['rate']/100);
            }
            $amounts[$tm_ranges[$i]['id']] = $amount;
        }

        return $amounts[$this->id];
    }
    
    public static function getRangeFromAmount($amount)
    {
        return TransferMemoStampDuty::firstWhere([
            ['min_price', '<', $amount],
            ['max_price', '>=', $amount]
        ]);
    }

    public static function calculateTotalFees($amount)
    {
        $tm = TransferMemoStampDuty::getRangeFromAmount($amount);
        $addAmount = $tm->addAmount();
        return (($amount - ($tm->min_price - 1.0)) * ($tm->rate/100)) + $addAmount;
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
