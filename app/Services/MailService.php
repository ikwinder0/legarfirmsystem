<?php
/**
 * Class MailService
 * @author ningmar
 * @package App\Services
 */

namespace App\Services;


use App\Models\User;
use App\Mail\CaseCreated;
use App\Mail\NewAppointment;
use App\Mail\CaseStatusChaged;
use Illuminate\Support\Facades\Mail;

class MailService
{

    public function sendStatusChangedMail($old_status, $case, $remarks) {
        $user = backpack_user();
        $customer = User::findorfail($case->customer);
        $businessPartner = User::find($case->introduced_by);
        $remarks = str_replace("\r\n", "<br/>", $remarks);
        if( $businessPartner && isset($businessPartner->email) )
        {
            Mail::to($customer->email)
                ->cc($businessPartner->email)
                ->send(new CaseStatusChaged($old_status,$case, $customer,$user, $remarks));
        }
        else
        {
            Mail::to($customer->email)
                ->send(new CaseStatusChaged($old_status,$case, $customer,$user, $remarks));
        }

    }
    public function sendCaseCreatedMail($case) {
        $user = backpack_user();
        $customer = User::findorfail($case->customer);
        $businessPartner = User::find($case->introduced_by);
        if( $businessPartner && isset($businessPartner->email) )
        {
            Mail::to($customer->email)
                ->cc($businessPartner->email)
                ->send(new CaseCreated($case, $customer,$user));
        }
        else
        {
            Mail::to($customer->email)
                ->send(new CaseCreated($case, $customer,$user));
        }
    }
    public function sendNewAppointmentMail($case, $date, $time)
    {   
        $admin_emails = User::role(['Admin', 'Super Admin'])->pluck('email')->toArray();
        Mail::to($admin_emails)->send(new NewAppointment($case, backpack_user(), $date, $time));
    }
}