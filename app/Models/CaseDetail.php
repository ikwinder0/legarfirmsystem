<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseDetail extends Model
{
    use CrudTrait,SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'case_details';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $casts = [
        'softcopy' => 'array',
        'tracks' => 'array'
    ];

    const _RECEIVE_ORDER = 'Receive Order';
    const _ASSIGN_PIC = 'Assign P.I.C';
    const _ARRANGE_FOR_SIGNING = 'Arrange For Signing';
    const _STAMPING_DATE = 'Stamping Date';
    const _BANK_EXECUTION_OR_REQUEST_REDEMPTION = 'Bank Execution / Request Redemption';
    const _ADVICE_1ST_RELEASE = 'Advise 1st Release';
    const _DISCHARGE_OF_CHARGE = 'Discharge Of Charge';
    const _PRESENT_MOT = 'Present MOT';
    const _ADVISE_FINAL_RELEASE = 'Advise Final Release';
    const _COMPLETION = 'Completion';

    const _STATUS = [
        self::_RECEIVE_ORDER,
        self::_ASSIGN_PIC,
        self::_ARRANGE_FOR_SIGNING,
        self::_STAMPING_DATE,
        self::_BANK_EXECUTION_OR_REQUEST_REDEMPTION,
        self::_ADVICE_1ST_RELEASE,
        self::_DISCHARGE_OF_CHARGE,
        self::_PRESENT_MOT,
        self::_ADVISE_FINAL_RELEASE,
        self::_COMPLETION,
    ];
    const _STATUS_OPTIONS = [
        self::_RECEIVE_ORDER => self::_RECEIVE_ORDER,
        self::_ASSIGN_PIC =>self::_ASSIGN_PIC,
        self::_ARRANGE_FOR_SIGNING => self::_ARRANGE_FOR_SIGNING,
        self::_STAMPING_DATE =>self::_STAMPING_DATE,
        self::_BANK_EXECUTION_OR_REQUEST_REDEMPTION =>self::_BANK_EXECUTION_OR_REQUEST_REDEMPTION,
        self::_ADVICE_1ST_RELEASE => self::_ADVICE_1ST_RELEASE,
        self::_DISCHARGE_OF_CHARGE =>self::_DISCHARGE_OF_CHARGE,
        self::_PRESENT_MOT => self::_PRESENT_MOT,
        self::_ADVISE_FINAL_RELEASE =>self::_ADVISE_FINAL_RELEASE,
        self::_COMPLETION =>self::_COMPLETION,
    ];
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function introducedBy() {
        return $this->belongsTo(User::class, 'introduced_by');
    }

    public function customers() {
        return $this->belongsTo(User::class,'customer');
    }

    public function statusLogs() {
        return $this->hasMany(CaseDetailStatusLog::class,'case_detail_id', 'id')->orderBy('id', 'DESC');
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
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setSoftcopyAttribute($value)
    {
        $attribute_name = "softcopy";
        $disk = "public";
        $destination_path = "case_details/softcopy";

        $this->uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);
    }
    public function getStatus() {
        return  $this::_STATUS[$this->status];
    }


    public function setTracksAttribute($status) {
        $tracks = $this->tracks ?? [];
        array_push($tracks,[
            'status' => $status,
            'user_id' => backpack_user()->id,
            'full_name' => backpack_user()->name,
            'time' => date('Y-m-d H:i:s')
        ]);
        $this->attributes['tracks'] = json_encode($tracks);
    }
    public function openStatus($crud = false)
    {
        return '<a class="btn btn-sm btn-link" type="button"  data-id="'.$this->id.'" onclick="openModal(this)"
            data-toggle="tooltip" title="Change Status"><i class="la la-check"></i> Change Status</a>';
    }

    public function getCaseDetailStatusLog() {
        $statusLogs = $this->statusLogs;
        $result = "";

        if(count($statusLogs) > 0)
        {
            $result = "<table class='table table-striped mb-0' style='width: 1024px; border: 1px solid #e0e5ec; white-space: initial'>";
            $result .= "<thead>";
            $result .= "<td><b>Status</b></td>";
            $result .= "<td><b>DateTime</b></td>";
            $result .= "<td><b>Description</b></td>";
            $result .= "<td><b>Status Remarks</b></td>";
            $result .= "</thead>";
            $result .= "<tbody>";
            foreach ($statusLogs as $statusLog) {

                $lengthDescription = strlen($statusLog->description);
                if($lengthDescription > 0 )
                {
                    $descriptionReadMoreText = "Read More";
                }
                else
                {
                    $descriptionReadMoreText = "";
                }

                $lengthRemarks = strlen($statusLog->remarks);
                if($lengthRemarks > 0 )
                {
                    $remarksReadMoreText = "Read More";
                }
                else
                {
                    $remarksReadMoreText = "";
                }

                $result .= "<tr>";
                $result .= "<td>" . $statusLog->status . "</td>";
                $result .= "<td>" . $statusLog->created_at . "</td>";
                $result .= "<td><div class='wrapper'><div class='read-less'><div style='font-family: unset; font-size: unset; width: 500px; white-space: break-spaces;'>" . $statusLog->description . "</div></div><a href='#'>" . $descriptionReadMoreText. "</a></td>";
                $result .= "<td><div class='wrapper'><div class='read-less'><div style='font-family: unset; font-size: unset; width: 800px; white-space: break-spaces;'>" . $statusLog->remarks . "</div></div><a href='#'>" . $remarksReadMoreText. "</a></td>";
                $result .= "</tr>";
            }
            $result .= "</tbody>";
            $result .= "</table>";
        }

        return $result;
    }
}
