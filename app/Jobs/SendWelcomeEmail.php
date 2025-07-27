<?php

namespace App\Jobs;

use App\Mail\NotifyAdminOfNewInstall;
use App\Mail\WelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $shop;
    public function __construct(array $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->shop['email'])->send(new WelcomeEmail($this->shop));
        Mail::to(env('ADMIN_EMAIL') ?? 'support@sfaddons.com')->send(new NotifyAdminOfNewInstall($this->shop));
    }
}
