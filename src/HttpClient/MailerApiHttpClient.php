<?php

namespace App\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MailerApiHttpClient
{
    private HttpClientInterface $client;
    private string $mailerAppUrl;
    private string $mailerApiHeader;
    private string $mailerApiKey;

    public function __construct(
        HttpClientInterface $client,
        string $mailerAppUrl,
        string $mailerApiHeader,
        string $mailerApiKey
    ) {
        $this->client = $client;
        $this->mailerAppUrl = $mailerAppUrl;
        $this->mailerApiHeader = $mailerApiHeader;
        $this->mailerApiKey = $mailerApiKey;
    }

    public function sendMail(array $data): string
    {
        $response = $this->client->request('POST', $this->mailerAppUrl . 'send-mail', [
            'headers' => [
                'Content-Type' => 'application/json',
                $this->mailerApiHeader => $this->mailerApiKey,
            ],
            'body' => json_encode($data),
        ]);

        return $response->getContent();
    }
}
