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

    /**
     * @throws TransportExceptionInterface
     */
    public function sendNewUrls($endpoint): void
    {
        // Получаем все URL, которые еще не были отправлены
        $urlRepository = $this->entityManager->getRepository(Url::class);
        $urls = $urlRepository->findUnsentUrls();

        foreach ($urls as $url) {
            // Подготовка данных для отправки
            $data = [
                'url' => $url->getUrl(),
                'created_date' => $url->getCreatedDate()->format('Y-m-d H:i:s'),
            ];

            // Отправка данных на указанный endpoint
            try {
                // Создаем HTTP клиент
                $client = HttpClient::create();
                // Отправляем запрос
                $response = $client->request('POST', $endpoint, [
                    'json' => $data,
                ]);

                // Проверка успешного ответа
                if ($response->getStatusCode() === 200) {
                    // Обновляем статус отправленного URL
                    $url->setSent(true);
                    // Сохраняем изменения в базе данных
                    $this->entityManager->flush();
                }
            } catch (\Exception $e) {
                // Логируем ошибки (можно просто выводить в консоль)
                error_log("Error sending URL: " . $e->getMessage());
            }
        }
    }
}