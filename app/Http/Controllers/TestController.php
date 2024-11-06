<?php

namespace App\Http\Controllers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class TestController extends Controller
{

    public function test()
    {
        return Response::json(array(
            'message'   =>  'Run Successful'
        ));
    }

    public function phpInfo()
    {
        phpinfo();
    }
}
