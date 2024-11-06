<?php

namespace App\Mail;

use App\Models\User;
use App\Models\CaseDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentDecided extends Mailable
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
    private $googleCalendarLink;

    public function __construct(CaseDetail $case, User $user, $date, $time, $status, $googleCalendarLink)
    {
        $this->case = $case;
        $this->user = $user;
        $this->date = $date;
        $this->time = $time;
        $this->status = $status;
        $this->googleCalendarLink = $googleCalendarLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'mails.appointment_decided',
            [
                'user' => $this->user,
                'case' => $this->case,
                'date' => $this->date,
                'time' => $this->time,
                'status' => $this->status,
                'googleCalendarLink' => $this->googleCalendarLink
            ]
        )
        ->subject('Appointment ' . strtolower($this->status));
    }
}
