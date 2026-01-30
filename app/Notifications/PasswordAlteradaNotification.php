<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordAlteradaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ?string $ip = null,
        public ?string $userAgent = null,
        public ?string $quando = null, // string já formatada (opcional)
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Email de confirmação: “A palavra-passe foi alterada”.
     */
    public function toMail($notifiable): MailMessage
    {
        $appName = config('app.name', 'Communico');
        $nome = trim((string)($notifiable->nome ?? $notifiable->name ?? ''));
        $greeting = $nome !== '' ? "Olá, {$nome}!" : 'Olá!';

        $quando = $this->quando ?: now()->format('Y-m-d H:i');
        $loginUrl = url(route('login', [], false));

        $m = (new MailMessage)
            ->subject("Palavra-passe alterada — {$appName}")
            ->greeting($greeting)
            ->line('A palavra-passe da sua conta foi alterada com sucesso.')
            ->line("Data/hora: {$quando}");

        if (!empty($this->ip)) {
            $m->line("IP: {$this->ip}");
        }

        return $m
            ->line('Se não reconhece esta alteração, recomendamos que contacte um administrador de imediato e faça uma reposição de palavra-passe.')
            ->action('Iniciar sessão', $loginUrl);
    }
}
