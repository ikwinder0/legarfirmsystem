<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CaseDetail;
use App\Models\CaseDetailStatusLog;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CaseController extends Controller
{

    public function index(Request $request)
    {
        $search_term = $request->input('q');

        $model = new CaseDetail();
        $model = $model->where('customer', $request->customer);

        if ($search_term)
        {
            $model = $model->where('title', 'LIKE', '%'.$search_term.'%')->get();
        }
        else
        {
            $model = $model->where('id', '>', 0)->get();
        }

        return response()->json([
            'data' =>$model
        ],200);
    }

    public function getTimeSlot(Request $request) {
         $times = TimeSlot::where('date', $request->date)
             ->select('time_slots')
             ->first();
         $result = [];
         if($times)
             foreach ($times['time_slots'] as $time) {
                 array_push($result,[
                     'value' => $time,
                     'label'=>Carbon::parse($time)->format('H:i a')
                 ]);
             }
         return response()->json([
             'available_times' => $result
         ]);
    }

    public function getLatestCaseDetailStatusLog(Request $request)
    {
        $caseDetailId = $request->case_detail_id;
        $status = $request->status;

        $caseDetailStatusLog = CaseDetailStatusLog::where('case_detail_id', $caseDetailId)
            ->where('status', $status)
            ->orderBy('id', 'DESC')
            ->first();

        return $caseDetailStatusLog;
    }
}
