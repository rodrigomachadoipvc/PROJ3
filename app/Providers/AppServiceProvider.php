<?php

namespace App\Providers;

use App\Mail\Transports\BrevoTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Illuminate\Mail\MailManager;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::component('main-layout', \Illuminate\View\Component::class);

        $this->app->make(MailManager::class)->extend('brevo', function ($config) {

            $fromAddress = $config['from']['address'] ?? env('MAIL_FROM_ADDRESS');
            $fromName    = $config['from']['name']    ?? env('MAIL_FROM_NAME');

            return new BrevoTransport(
                apiKey: env('BREVO_API_KEY'),
                fromAddress: $fromAddress,
                fromName: $fromName
            );
        });
    }
}