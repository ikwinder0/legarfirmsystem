<?php

namespace App\Mail;

use App\Models\RunnerTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
	private $task;
	private $old_status;
    private $user;
    private $status;
    public function __construct($old_status, RunnerTask $task, $user, $status)
    {
        $this->task = $task;
		$this->old_status = $old_status;
        $this->user = $user;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.task_status_changed',
            [
                'user' => $this->user,
                'task' => $this->task,
                'status' => $this->status,
                'old_status' => $this->old_status
            ]
        );
    }
}
