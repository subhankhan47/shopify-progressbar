<?php

namespace App\Jobs;

use App\Mail\NotifyAdminOfUninstall;
use App\Mail\UninstallEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendUninstallEmail implements ShouldQueue
{
    use Queueable;
    public $shop;

    /**
     * Create a new job instance.
     * @param $shop
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->shop->email)->send(new UninstallEmail($this->shop));
        Mail::to(env('ADMIN_EMAIL') ?? 'support@sfaddons.com')->send(new NotifyAdminOfUninstall($this->shop));
    }
}
