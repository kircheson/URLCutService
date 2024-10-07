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

    public function addUrl(string $urlString, string $createdDate): array
    {
        try {
            $hash = substr(md5($urlString), 0, 14);

            $existingUrl = $this->entityManager->getRepository(Url::class)->findOneBy(['hash' => $hash]);
            if ($existingUrl) {
                return ['error' => 'URL with this hash already exists', 'status' => 409];
            }

            $url = new Url();
            $url->setUrl($urlString);

            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', trim($createdDate));
            if ($date === false) {
                return ['error' => 'Invalid date format', 'status' => 400];
            }
            $url->setCreatedDate($date);
            $url->setHash($hash);

            $this->entityManager->persist($url);
            $this->entityManager->flush();

            return ['status' => 'success', 'message' => 'URL added successfully', 'http_code' => 201];
        } catch (\Exception $e) {
            return ['error' => 'Could not save URL: ' . $e->getMessage(), 'status' => 500];
        }
    }

    public function getStats(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, ?string $domain): array
    {
        $uniqueUrls = $this->entityManager->getRepository(Url::class)->findUniqueUrlsByDateRange($startDate, $endDate);
        $uniqueUrlsCount = count($uniqueUrls);

        if ($domain) {
            $uniqueDomainUrls = $this->entityManager->getRepository(Url::class)->findUniqueUrlsByDomain($domain);
            $uniqueDomainCount = count($uniqueDomainUrls);
        } else {
            $uniqueDomainCount = 0;
        }

        return [
            'unique_urls_count' => (int)$uniqueUrlsCount,
            'unique_domain_count' => (int)$uniqueDomainCount,
        ];
    }
}