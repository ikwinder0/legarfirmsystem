<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CasePointTransaction extends Model
{
    use HasFactory, CrudTrait;
    protected $table = 'case_point_transactions';
    protected $guarded = ['id'];

    const POINT_RECEIVED = 'point received';
    const POINT__AFTER_UPDATE = 'points updated';

    protected $casts = [
        'case_detail' => 'array'
    ];

    public function caseDetail() {
        return $this->belongsTo(CaseDetail::class,'case_id');
    }

    public function businessPartner() {
        return $this->belongsTo(User::class, 'business_partner');
    }

    public function customers() {
        return $this->belongsTo(User::class,'customer_id');
    }

    public function getSubPointAttribute() {
        return  ($this->updated_current_points??0)  - ($this->old_points??0) ;
    }
}
