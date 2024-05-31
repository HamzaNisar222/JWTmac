<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $confirmationUrl;

    public function __construct($user, $confirmationUrl)
    {

        $this->user = $user;

        $this->confirmationUrl = $confirmationUrl;
    }

    public function build()
    {
        return $this->view('emails.user_confirm')
        ->with([
            'confirmationUrl' => $this->confirmationUrl,
            'user' => $this->user,
        ]);
    }
}
