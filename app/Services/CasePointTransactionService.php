<?php
/**
 * Class CasePointService
 * @author ningmar
 * @package App\Services
 */

namespace App\Services;



use App\Models\CasePointTransaction;

use App\Models\User;

class CasePointTransactionService
{
    public function storeFunction($data) {
        return CasePointTransaction::create($data);
    }

    public function casePointTransactionByCase($case) {
        return CasePointTransaction::where('case_id','=', $case)
            ->latest('created_at')
            ->firstorfail();
    }

    public function saveCasePointOnCreate($case, $case_point) {
        $user = User::findorfail($case->introduced_by);
        $old_point = $user->case_points ?? 0;
        $new_point = $old_point + $case_point->points;
        $user->case_points = $new_point;
        $user->save();
        $_data = [
            'price' => $case->price,
            'case_point'=> $case_point->points,
            'business_partner'=>$case->introduced_by,
            'customer_id' => $case->customer,
            'created_by' => backpack_user()->id,
            'status' => CasePointTransaction::POINT_RECEIVED,
            'old_points' => $old_point,
            'updated_current_points' => $new_point,
            'remarks' => 'points '.$case_point->points.' received from the case of price '.$case->price,
            'case_detail' => [
                'business_partner' => $user,
                'case_detail' => $case,
                'case_point' => $case_point
            ],
            'case_id' => $case->id
        ];
        $this->storeFunction($_data);

    }

    public function saveCasePointOnUpdate($case, $case_point) {
        $old_transaction = $this->casePointTransactionByCase($case->id);
        if($case->introduced_by != $old_transaction->business_partner)
            dd('This case is yet to handled. Different business partner on update');
        $old_case_point = $old_transaction->case_point;
        $user = User::findorfail($case->introduced_by);
        $old_point = $user->case_points;
        $new_point = $old_point + $case_point->points - $old_case_point;
        $user->case_points = $new_point;
        $user->save();
        $_data = [
            'price' => $case->price,
            'case_point'=> $case_point->points,
            'business_partner'=>$case->introduced_by,
            'customer_id' => $case->customer,
            'created_by' => backpack_user()->id,
            'status' => CasePointTransaction::POINT__AFTER_UPDATE,
            'old_points' => $old_point,
            'updated_current_points' => $new_point,
            'remarks' => 'points '.$case_point->points.' received after update from the case of price '.$case->price,
            'case_detail' => [
                'business_partner' => $user,
                'case_detail' => $case,
                'case_point' => $case_point
            ],
            'case_id' => $case->id
        ];
        $this->storeFunction($_data);

    }
}