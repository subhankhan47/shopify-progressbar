<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $shop;

    /**
     * Create a new message instance.
     * @param $shop
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    public function build()
    {
        return $this->subject('Welcome to ' . (env('APP_NAME') ?? 'SF Reward Bar'))
            ->markdown('emails.welcome')
            ->with(['shop' => $this->shop]);
    }
}
