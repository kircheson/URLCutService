<?php

namespace App\Service;

use App\Entity\Url;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;

class UrlEncoderService
{
    private $urlRepository;
    private $entityManager;

    public function __construct(UrlRepository $urlRepository, EntityManagerInterface $entityManager)
    {
        $this->urlRepository = $urlRepository;
        $this->entityManager = $entityManager;
    }

    public function encodeUrl(string $inputUrl): ?string
    {
        $existingUrl = $this->urlRepository->findOneBy(['url' => $inputUrl]);

        if ($existingUrl) {
            return $existingUrl->getHash();
        }

        return null;
    }

    public function validateUrl(string $url): ?string
    {
        if (empty($url)) {
            return 'URL is required.';
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Invalid URL format.';
        }

        return null;
    }
}