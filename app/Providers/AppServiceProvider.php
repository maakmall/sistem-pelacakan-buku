<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::globalOptions([
            'verify' => false,
            // 'proxy' => 'http://scraperapi:9abba389b59aeaf6528d5eacbb5c65ea@proxy-server.scraperapi.com:8001',
            // 'proxy' => 'http://4a174fb0e3e243708cdff69a9842d5298ba37a3856a:@proxy.scrape.do:8080',
        ]);
    }
}
