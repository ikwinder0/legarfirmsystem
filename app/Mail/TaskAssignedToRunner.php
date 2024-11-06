<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssignedToRunner extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
	private $remarks;
	private $title;
    private $user;
    private $description;
    public function __construct(User $user, $title, $description, $remarks)
    {
        $this->user = $user;
		$this->title = $title;
        $this->remarks = $remarks;
        $this->description = $description;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.assign_task_to_runner',
            [
                'user' => $this->user,
                'title' => $this->title,
                'remarks' => $this->remarks,
                'description' => $this->description
            ]
        );
    }
}
