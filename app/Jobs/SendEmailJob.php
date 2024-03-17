<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Number of times to attempt the job
    protected $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function handle()
    {
        try {
            // Attempt to send the email
            Mail::send($this->emailData['view'], $this->emailData['data'], function ($message) {
                $message->from($this->emailData['from'], $this->emailData['from_name'])
                        ->to($this->emailData['to'], $this->emailData['to_name'])
                        ->subject($this->emailData['subject']);
            });
        } catch (\Exception $e) {
            // If sending fails, the job will be retried
            throw new \Exception("Error sending email: " . $e->getMessage());
        }
    }
}
