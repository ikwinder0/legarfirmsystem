<?php

namespace App\Models;

use DateTime;
use DateTimeZone;
use Spatie\CalendarLinks\Link;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Appointment extends Model
{
    use CrudTrait, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'appointments';
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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function caseDetail()
    {
        return $this->belongsTo(CaseDetail::class, 'case_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
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

    public function setAppointmentTimeAttribute()
    {
        $this->attributes['appointment_time'] = request('date') . ' ' . request('time');
    }

    public function setCustomerIdAttribute($val = null)
    {
        $this->attributes['customer_id'] = $val ?? backpack_user()->id;
    }

    public function getGoogleCalendarLink()
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', $this->appointment_time);
        $to = date('Y-m-d H:i:s',strtotime('+1 hour',strtotime($this->appointment_time)));
        $to = DateTime::createFromFormat('Y-m-d H:i:s', $to);

        $linkToAppointment = "<a href=" . route('appointment.show', ['id' => $this->id]) . ">here</a>";

        $link = Link::create(env('APP_NAME') . ' Appointemnt', $from, $to)
            ->description('You can check more details ' . $linkToAppointment . '.');

        return $link->google();
    }
}
