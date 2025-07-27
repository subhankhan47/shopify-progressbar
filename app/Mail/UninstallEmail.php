<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UninstallEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $shop;

    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    public function build()
    {
        return $this->subject('We’re sorry to see you go – ' . (env('APP_NAME') ?? 'SF Reward Bar'))
            ->markdown('emails.uninstall')
            ->with(['shop' => $this->shop]);
    }
}
