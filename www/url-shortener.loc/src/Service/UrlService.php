<?php

namespace App\Service;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UrlService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addUrl(string $urlString, string $createdDate): JsonResponse
    {
        // Создаем новую сущность Url
        try {
            $url = new Url();
            $url->setUrl($urlString);

            // Устанавливаем дату создания из запроса
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', trim($createdDate));
            if ($date === false) {
                return new JsonResponse(['error' => 'Invalid date format'], 400);
            }
            $url->setCreatedDate($date);

            // Сохраняем в базе данных
            $this->entityManager->persist($url);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'URL added successfully'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Could not save URL: ' . $e->getMessage()], 500);
        }
    }

    public function getStats(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, ?string $domain): array
    {
        // Получаем количество уникальных URL за заданный промежуток времени.
        $uniqueUrlsCount = count($this->entityManager->getRepository(Url::class)->findUniqueUrlsByDateRange($startDate, $endDate));

        // Получаем количество уникальных URL с указанным доменом.
        if ($domain) {
            $uniqueDomainCount = count($this->entityManager->getRepository(Url::class)->findUniqueUrlsByDomain($domain));
        } else {
            $uniqueDomainCount = 0; // Если домен не указан, устанавливаем в 0.
        }

        return [
            'unique_urls_count' => $uniqueUrlsCount,
            'unique_domain_count' => $uniqueDomainCount,
        ];
    }
}