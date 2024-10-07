<?php

namespace App\Service;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UrlSenderService
{
    private $entityManager;
    private $endpoint;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function sendNewUrls($endpoint): void
    {
        $urlRepository = $this->entityManager->getRepository(Url::class);
        $urls = $urlRepository->findUnsentUrls();

        foreach ($urls as $url) {
            $data = [
                'url' => $url->getUrl(),
                'created_date' => $url->getCreatedDate()->format('Y-m-d H:i:s'),
            ];

            try {
                $client = HttpClient::create();
                $response = $client->request('POST', $endpoint, [
                    'json' => $data,
                ]);

                if ($response->getStatusCode() === 200) {
                    $url->setSent(true);
                    $this->entityManager->flush();
                }
            } catch (\Exception $e) {
                error_log("Error sending URL: " . $e->getMessage());
            }
        }
    }
}