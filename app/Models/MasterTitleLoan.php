<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class MasterTitleLoan extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'master_title_loans';
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
            ['cid', 3],
            ['section', 'professional_charges']
        ])->orderBy('pos')->get();
    }

    public static function reimbursement_items()
    {
        return CalculatorItem::where([
            ['cid', 3],
            ['section', 'reimbursements']
        ])->orderBy('pos')->get();
    }

    public static function disbursement_items()
    {
        return CalculatorItem::where([
            ['cid', 3],
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
