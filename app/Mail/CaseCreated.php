<?php

namespace App\Mail;

use App\Models\CaseDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CaseCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $case;
    private $user;
    private $admin;
    private $show_creds = false;
    public function __construct(CaseDetail $case,$user, $admin)
    {
        $this->case = $case;
        $this->user = $user;
        $this->admin = $admin;

        if (!$user->creds_received) {
            $this->show_creds = true;
            $user->creds_received = true;
            $user->save();
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->markdown('mails.case_created',
            [
                'user' => $this->user,
                'case' => $this->case,
                'admin' => $this->admin,
                'show_creds' => $this->show_creds
            ]
        );

        $softcopies = $this->case->softcopy;
        if ($softcopies) {
            if (is_array($softcopies)) {
                foreach ($softcopies as $slip) {
                    $mail->attachFromStorageDisk('public', $slip);
                }
            } else {
                $mail->attachFromStorageDisk('public', $this->case->softcopy);
            }
        }


        return $mail;
    }
}
