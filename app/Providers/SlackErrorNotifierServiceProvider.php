<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Monolog\Handler\SlackHandler;
use Monolog\Logger;

class SlackErrorNotifierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (true /**!$this->app->environment('local')**/) {
            $token = env('ERROR_SLACK_API_TOKEN', '');
            $channel = env('ERROR_SLACK_CHANNEL', '');

            if (!empty($token) && !empty($channel)) {
                $monolog = Log::getLogger();
                $slackHandler = new SlackHandler($token, $channel, 'Monolog', true, null, Logger::ERROR);
                $result     = $monolog->pushHandler($slackHandler);
            }
        }
    }

    private function lineAlert(){
        $msg    = "Ada error, Silahkan lihat Slack!, dari Line Pablow " . env('APP_ENV', '') . ' - ' . Logger::ERROR;
        $url    = "https://mr-notification.mjniuz.com/api/send?message=" . $msg . "&userID=ZYxYTj5evf";
        @file_get_contents($url);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
