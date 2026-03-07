<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class SendFeedback extends Mailable
{
    use Queueable, SerializesModels;
    public $text;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $user, string $text)
    {
        $this->text = $text;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject_feedback = 'Feedback from Customer: ' . $this->user;
        return $this->from($this->user)->subject($subject_feedback)->view('emails.users.feedback')->with('user', $this->user, 'message', $this->text);
    }
}
