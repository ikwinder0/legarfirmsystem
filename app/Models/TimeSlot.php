<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSlot extends Model
{
    use CrudTrait,SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'time_slots';
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

    protected $casts = [
        'time_slots' => 'array'
    ];

    public static function hourlyTimes() {
        $_arr = [];
        for($i = 9; $i < 18; $i++) {
            $hr_time = str_pad($i,2,'0',STR_PAD_LEFT).':00';
            $_arr[$hr_time] = (new DateTime($hr_time))->format('H:i a');
        }
        return $_arr;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function caseDetail() {
        return $this->belongsTo(CaseDetail::class,'case_id');
    }

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

    public function formatTimeSlot() {
        return $this->time_slots ? $this->time_slots[0].'...'.$this->time_slots[count($this->time_slots)-1]: 'N/A';
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
