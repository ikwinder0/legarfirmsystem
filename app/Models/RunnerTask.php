<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;


class RunnerTask extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'runner_tasks';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
	
	protected $casts = [
        'attachments' => 'array',
    ];
	
	const PROCESS = 'Process';
    const COMPLETED = 'Completed';
	
	const _STATUS = [
			self::PROCESS,
            self::COMPLETED,
    ];
	
	const _STATUS_OPTIONS = [
			self::PROCESS => self::PROCESS,
            self::COMPLETED =>self::COMPLETED,
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
	
	public function runner(){
        return $this->belongsTo(User::class, 'user_id');
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
	public function setAttachmentsAttribute($value)
	{
		$attribute_name = "attachments";
		$disk = "public";
		$destination_path = "runnertasks";

		$this->uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);
	}

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
	
	public function openStatus($crud = false)
    {
        return '<a class="btn btn-sm btn-link" type="button"  data-id="'.$this->id.'" onclick="openModal(this)"
            data-toggle="tooltip" title="Change Status"><i class="la la-check"></i> Change Status</a>';
    }
}
