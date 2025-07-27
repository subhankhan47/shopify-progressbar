<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyAdminOfUninstall extends Mailable
{
    use Queueable, SerializesModels;

    public $shop;

    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    public function build()
    {
        return $this->subject(
            (env('APP_NAME') ?? 'SF Reward Bar') . " | App Uninstalled: {$this->shop->name}"
        )->markdown('emails.admin_uninstall')
            ->with([
                'shop' => $this->shop,
                'uninstalled_at' => now()->toDayDateTimeString()
            ]);
    }
}
