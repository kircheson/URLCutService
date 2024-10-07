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
    public function decodeUrl(string $hash): array
    {
        $url = $this->urlRepository->findOneByHash($hash);

        if (empty($url)) {
            return ['error' => 'Non-existent hash.', 'status' => 404];
        }

        $currentDate = new DateTimeImmutable();

        if ($url->getExpiredAt() < $currentDate) {
            return ['error' => 'This URL has expired.', 'status' => 410];
        }

        return ['url' => $url->getUrl(), 'status' => 200];
    }
}