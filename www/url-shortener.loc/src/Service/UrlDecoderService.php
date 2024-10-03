<?php

namespace App\Service;

use App\Entity\Url;
use DateTimeImmutable;
use App\Repository\UrlRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;

class UrlDecoderService
{
    private $urlRepository;

    public function __construct(UrlRepository $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function decodeUrl(string $hash): JsonResponse
    {
        $url = $this->urlRepository->findOneByHash($hash);

        if (empty($url)) {
            return new JsonResponse(['error' => 'Non-existent hash.'], 404);
        }

        // Проверка на истечение срока действия
        $currentDate = new DateTimeImmutable();

        if ($url->getExpiresAt() < $currentDate) {
            return new JsonResponse(['error' => 'This URL has expired.'], 410); // Код 410 Gone
        }

        return new JsonResponse(['url' => $url->getUrl()]);
    }
}