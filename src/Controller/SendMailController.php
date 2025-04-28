<?php

namespace App\Controller;

use App\HttpClient\MailerApiHttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SendMailController extends AbstractController
{
    private MailerApiHttpClient $mailerApiHttpClient;

    public function __construct(MailerApiHttpClient $mailerApiHttpClient)
    {
        $this->mailerApiHttpClient = $mailerApiHttpClient;
    }

    #[Route('/send-demo-mail', name: 'send_demo_mail')]
    public function sendMail(): JsonResponse
    {
        $data = [
            'to' => 'demoprof@demo.fr',
            'subject' => 'Test depuis le backoffice',
            'message' => 'Coucou, ceci est un test envoyé depuis le backoffice via le microservice Mailer !'
        ];

        $responseContent = $this->mailerApiHttpClient->sendMail($data);

        return new JsonResponse([
            'message' => 'Mail envoyé avec succès via le microservice.',
            'mailer_response' => $responseContent
        ]);
    }
}
