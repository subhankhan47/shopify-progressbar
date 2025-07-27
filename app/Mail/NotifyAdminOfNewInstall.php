<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyAdminOfNewInstall extends Mailable
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
        return $this->subject(
            (env('APP_NAME') ?? 'SF Reward Bar') . " | New App Install: {$this->shop['storeName']}")
            ->markdown('emails.admin_new_install')
            ->with([
                'shop' => $this->shop,
                'installed_at' => now()->toDayDateTimeString()
            ]);
    }
}
