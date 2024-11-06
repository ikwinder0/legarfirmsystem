<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];
}
