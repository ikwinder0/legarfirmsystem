<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $role = $request->input('role');
        $roles = $request->input('roles');
        $model = new User();

        if($role)
            $model = $model->whereHas('roles', function ($q) use ($role) {
                return $q->where('roles.name', $role);
            });

        if( $roles == "All Business Partner" )
            $model = $model->whereHas('roles', function ($q) use ($role) {
                return $q->where('roles.name', "Business Partner")->orWhere('roles.name', "Senior Business Partner");
            });

        if ($search_term)
        {
            $model = $model->where('name', 'LIKE', '%'.$search_term.'%')->get();
        }
        else
        {
            $model = $model->where('id', '>', 0)->get();
        }

        return response()->json([
            'data' =>$model
        ],200);
    }
}
