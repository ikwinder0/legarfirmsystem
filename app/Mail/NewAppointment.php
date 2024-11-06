<?php

namespace App\Mail;

use App\Models\CaseDetail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAppointment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $case;
    private $user;
    private $date;
    private $time;
    public function __construct(CaseDetail $case, User $user, $date, $time)
    {
        $this->case = $case;
        $this->user = $user;
        $this->date = $date;
        $this->time = $time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'mails.new_appointment',
            [
                'user' => $this->user,
                'case' => $this->case,
                'date' => $this->date,
                'time' => $this->time
            ]
        );
    }
}
