<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Service\UrlEncoderService;
use App\Service\UrlDecoderService;
use App\Service\UrlSenderService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UrlController extends AbstractController
{
    private $urlEncoderService;
    private $urlDecoderService;
    private $urlSenderService;

    public function __construct(UrlEncoderService $urlEncoderService, UrlDecoderService $urlDecoderService, UrlSenderService $urlSenderService)
    {
        $this->urlEncoderService = $urlEncoderService;
        $this->urlDecoderService = $urlDecoderService;
        $this->urlSenderService = $urlSenderService;
    }

    /**
     * @Route("/encode-url", name="encode_url", methods={"POST"})
     */
    public function encodeUrl(Request $request): JsonResponse
    {
        $inputUrl = $request->get('url');
        $error = $this->urlEncoderService->validateUrl($inputUrl);

        if ($error) {
            return $this->json([
                'error' => $error
            ], 400);
        }

        $hash = $this->urlEncoderService->encodeUrl($inputUrl);

        if ($hash === null) {
            return $this->json([
                'error' => 'URL not found in the database.'
            ], 404);
        }

        return $this->json([
            'hash' => $hash
        ]);
    }

    /**
     * @Route("/decode-url", name="decode_url", methods={"POST"})
     * @throws NonUniqueResultException
     */
    public function decodeUrl(Request $request): JsonResponse
    {
        $hash = $request->get('hash');

        $result = $this->urlDecoderService->decodeUrl($hash);

        return new JsonResponse($result, $result['status']);
    }

    /**
     * @Route("/redirect/{hash}", name="redirect_url", methods={"GET"})
     * @throws NonUniqueResultException
     */
    public function redirectUrl($hash, UrlRepository $urlRepository): RedirectResponse
    {
        $url = $urlRepository->findOneByHash($hash);

        if (!$url) {
            throw $this->createNotFoundException('URL not found');
        }

        return $this->redirect($url->getUrl());
    }

    /**
     * @Route("/api/send-urls", name="send_urls", methods={"POST"})
     * @throws TransportExceptionInterface
     */
    public function sendUrls(): JsonResponse
    {
        try {
            $this->urlSenderService->sendNewUrls();
            return new JsonResponse(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
