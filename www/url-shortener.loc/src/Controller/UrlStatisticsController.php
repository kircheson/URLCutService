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

    #[Route("/api/urls", name: "add_url", methods: ["POST"])]
    public function addUrl(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['url']) || !isset($data['created_date'])) {
            return new JsonResponse(['error' => 'Invalid input'], 400);
        }

        $result = $this->urlService->addUrl($data['url'], $data['created_date']);

        if (isset($result['error'])) {
            return new JsonResponse(['error' => $result['error']], $result['status']);
        }

        return new JsonResponse(['status' => 'success', 'message' => 'URL added successfully'], 201);
    }

    #[Route("/api/urls/stats", name: "url_stats", methods: ["GET"])]
    public function getStats(Request $request): JsonResponse
    {
        try {
            // Получаем даты из параметров запроса (формат: YYYY-MM-DD-H-I-S)
            $startDate = new \DateTimeImmutable($request->query->get('start_date'));
            $endDate = new \DateTimeImmutable($request->query->get('end_date'));

            if ($startDate > $endDate) {
                return new JsonResponse(['error' => 'Start date must be earlier than end date.'], 400);
            }

            $domain = trim($request->query->get('domain'));

            $stats = $this->urlService->getStats($startDate, $endDate, $domain);

            return new JsonResponse(['status' => 'success', 'data' => $stats], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Could not retrieve statistics: ' . $e->getMessage()], 500);
        }
    }
}