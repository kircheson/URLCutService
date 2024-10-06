<?php

namespace App\Controller;

use App\Service\UrlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UrlStatisticsController extends AbstractController
{
    private $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * @Route("/api/urls", name="add_url", methods={"POST"})
     */
    public function addUrl(Request $request): JsonResponse
    {
        // Получаем данные из запроса
        $data = json_decode($request->getContent(), true);

        // Проверяем наличие необходимых данных
        if (!isset($data['url']) || !isset($data['created_date'])) {
            return new JsonResponse(['error' => 'Invalid input'], 400);
        }

        // Используем сервис для добавления URL
        return $this->urlService->addUrl($data['url'], $data['created_date']);
    }

    /**
     * @Route("/api/urls/stats", name="url_stats", methods={"GET"})
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            // Получаем даты из параметров запроса (формат: YYYY-MM-DD)
            if (!$startDate = new \DateTimeImmutable($request->query->get('start_date'))) {
                throw new \Exception("Invalid start date");
            }

            if (!$endDate = new \DateTimeImmutable($request->query->get('end_date'))) {
                throw new \Exception("Invalid end date");
            }

            // Получаем домен (если указан)
            $domain = trim($request->query->get('domain'));

            // Используем сервис для получения статистики
            return new JsonResponse($this->urlService->getStats($startDate, $endDate, $domain));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Could not retrieve statistics: ' . $e->getMessage()], 500);
        }
    }
}