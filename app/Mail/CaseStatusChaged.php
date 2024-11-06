<?php

namespace App\Mail;

use App\Models\CaseDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CaseStatusChaged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $case;
    private $old_status;
    private $user;
    private $admin;
    private $remarks;
    public function __construct($old_status,CaseDetail $case,$user, $admin, $remarks)
    {
        $this->old_status = $old_status;
        $this->case = $case;
        $this->user = $user;
        $this->admin = $admin;
        $this->remarks = $remarks;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.case_status_changed',
            [
                'user' => $this->user,
                'case' => $this->case,
                'old_status' => $this->old_status,
                'admin' => $this->admin,
                'remarks' => $this->remarks
            ]
        );
    }
}
