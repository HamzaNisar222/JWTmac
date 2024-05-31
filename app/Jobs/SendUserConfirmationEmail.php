<?php
namespace App\Jobs;


use Illuminate\Bus\Queueable;
use App\Mail\UserConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendUserConfirmationEmail implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $confirmationUrl;

    public function __construct($user, $confirmationUrl)
    {
        $this->user = $user;
        // dd($user);
        $this->confirmationUrl = $confirmationUrl;
    }

    public function handle()
    {

        try {

            Mail::to($this->user->email)->send(new UserConfirmationMail($this->user, $this->confirmationUrl));

                Response::success('Email sent successfully to ' . $this->user->email);
        } catch (\Exception $e) {
            Response::error('Failed to send email: ' . $e->getMessage());
        }
    }
}
