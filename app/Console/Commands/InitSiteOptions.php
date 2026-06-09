<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SiteOption;

class InitSiteOptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:init-options';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize site options';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keys = [
            // SMS
            'sms-user-name',
            'sms-password',
            'sms-sender-id',

            // Facebook
            'facebook-domain-verification-id',
            'facebook-pixel-key',
            'facebook-access-token',
            'facebook-event-name',
            'facebook-event-id',

            // Interakt
            'interakt-key',
            'interakt-template-name',
            'interakt-media-url',

            // RCS
            'rcs-user-id',
            'rcs-api-key',
            'rcs-template-id',

            // Other Messages
            'customer-message',
            'welcome-message',

            // SMS Templates
            'otp-sms',
            'complete-process-sms',
            'application-submitted-sms',
            'login-otp-sms',
            'welcome-sms',
            'payment-failed-sms',
            'account-sms',
        ];

        foreach ($keys as $key) {
            SiteOption::firstOrCreate(['option_key' => $key]);
        }

        $this->info('Site options initialized successfully!');
    }
}
