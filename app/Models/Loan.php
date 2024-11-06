<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'loans';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    // public $entry_withdrawal_caveat = 350.00;
    // public $statutory_declaration = 100.00;
    // public $affirmation_sd_fee = 40.00;
    // public $travelling_despatch = 150.00;
    // public $courier_postage = 150.00;
    // public $printing_stationery = 150.00;
    // public $misc = 100.00;
    // public $purchase_document_fees = 350.00;
    // public $sd_dfa = 20.00;
    // public $sd_letter_offer = 30.00;
    // public $sup_letter_offer = 0.00;
    // public $sd_charge_annexures = 40.00;
    // public $sd_statutory_declaration = 20.00;
    // public $land_search = 240.00;
    // public $reg_charge = 120.00;
    // public $reg_entry_withdrawal = 210.00;
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function totalProfessionalCharge()
    {
        $items = $this->pro_charge_items();
        $sum = 0;

        foreach ($items as $item) {
            if ($item->type_of_price == 'fix_price') {
                $sum += $item->price;
            } else {
                if (isset($this[$item->name]))
                    $sum += $this[$item->name];
                else
                    $sum += AddedSectionItem::where([
                        ['item_id', $this->id],
                        ['name', $item->name]
                    ])->first()->amount ?? 0;
            }
        }

        return $sum;
    }

    public function totalReimbursements()
    {
        $items = $this->reimbursement_items();
        $sum = 0;

        foreach ($items as $item) {
            if ($item->type_of_price == 'fix_price') {
                $sum += $item->price;
            } else {
                if (isset($this[$item->name]))
                    $sum += $this[$item->name];
                else
                    $sum += AddedSectionItem::where([
                        ['item_id', $this->id],
                        ['name', $item->name]
                    ])->first()->amount ?? 0;
            }
        }

        return $sum;
    }

    public function totalDisbursements()
    {
        $items = $this->disbursement_items();
        $sum = 0;

        foreach ($items as $item) {
            if ($item->type_of_price == 'fix_price') {
                $sum += $item->price;
            } else {
                if (isset($this[$item->name]))
                    $sum += $this[$item->name];
                else
                    $sum += AddedSectionItem::where([
                        ['item_id', $this->id],
                        ['name', $item->name]
                    ])->first()->amount ?? 0;
            }
        }

        return $sum;
    }

    public function subTotal()
    {
        return $this->totalProfessionalCharge() + $this->totalReimbursements() + $this->totalDisbursements();
    }

    public static function pro_charge_items()
    {
        return CalculatorItem::where([
            ['cid', 2],
            ['section', 'professional_charges']
        ])->orderBy('pos')->get();
    }

    public static function reimbursement_items()
    {
        return CalculatorItem::where([
            ['cid', 2],
            ['section', 'reimbursements']
        ])->orderBy('pos')->get();
    }

    public static function disbursement_items()
    {
        return CalculatorItem::where([
            ['cid', 2],
            ['section', 'disbursements']
        ])->orderBy('pos')->get();
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
