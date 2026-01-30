<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CommunicoResetPassword extends ResetPassword
{
    /**
     * Email “Esqueci-me da palavra-passe” (link de reposição) em PT-PT.
     */
    public function toMail($notifiable): MailMessage
    {
        $appName = config('app.name', 'Communico');
        $nome = trim((string)($notifiable->nome ?? $notifiable->name ?? ''));
        $greeting = $nome !== '' ? "Olá, {$nome}!" : 'Olá!';

        $expire = (int) config(
            'auth.passwords.' . config('auth.defaults.passwords', 'users') . '.expire',
            60
        );

        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject("Reposição de palavra-passe — {$appName}")
            ->greeting($greeting)
            ->line('Recebemos um pedido para repor a palavra-passe da sua conta.')
            ->action('Repor palavra-passe', $url)
            ->line("Este link expira em {$expire} minuto(s).")
            ->line('Se não fez este pedido, pode ignorar este email com segurança.');
    }

    /**
     * Gera o URL do reset (respeita createUrlUsing se existir).
     */
    protected function resetUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
