<?php

namespace App\Mail\Transports;

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use GuzzleHttp\Client;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;

class BrevoTransport extends AbstractTransport
{
    protected TransactionalEmailsApi $client;
    protected string $fromAddress;
    protected string $fromName;

    public function __construct(string $apiKey, string $fromAddress, string $fromName)
    {
        $this->fromAddress = $fromAddress;
        $this->fromName    = $fromName;

        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', $apiKey);

        $this->client = new TransactionalEmailsApi(new Client(), $config);

        parent::__construct();
    }

    /**
     * Envia o email usando a API da Brevo (Sendinblue)
     */
    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new TransportException('BrevoTransport só suporta instâncias de Email.');
        }

        // FROM
        $from = $email->getFrom()[0] ?? null;

        $fromEmail = $from?->getAddress() ?: $this->fromAddress;
        $fromName  = $from?->getName()    ?: $this->fromName;

        // TO
        $toList = [];
        foreach ($email->getTo() as $addr) {
            $toList[] = [
                'email' => $addr->getAddress(),
                'name'  => $addr->getName() ?: $addr->getAddress(),
            ];
        }

        // SUBJECT
        $subject = $email->getSubject() ?: '(Sem assunto)';

        // BODY
        $html = $email->getHtmlBody() ?: null;
        $text = $email->getTextBody() ?: strip_tags($html) ?: '';

        // Payload para a API
        $payload = new \SendinBlue\Client\Model\SendSmtpEmail([
            'sender' => [
                'email' => $fromEmail,
                'name'  => $fromName,
            ],
            'to' => $toList,
            'subject' => $subject,
            'htmlContent' => $html,
            'textContent' => $text,
        ]);

        try {
            $this->client->sendTransacEmail($payload);
        } catch (\Throwable $e) {
            throw new TransportException(
                'Erro ao enviar email via Brevo: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}   