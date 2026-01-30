<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CommunicoVerifyEmail extends BaseVerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verificação de email — ' . config('app.name'))
            ->greeting('Olá!')
            ->line('Recebeste este email porque foi criado um registo no ' . config('app.name') . '.')
            ->line('Para ativares a tua conta, confirma o teu endereço de email.')
            ->action('Confirmar email', $url)
            ->line('Se não criaste esta conta, podes ignorar esta mensagem.')
            ->salutation('Cumprimentos,' . "\n" . config('app.name'));
    }
}
