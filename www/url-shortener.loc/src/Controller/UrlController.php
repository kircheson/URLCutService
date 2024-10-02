<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Service\UrlEncoderService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UrlController extends AbstractController
{
    private $urlEncoderService;

    public function __construct(UrlEncoderService $urlEncoderService)
    {
        $this->urlEncoderService = $urlEncoderService;
    }

    /**
     * @Route("/encode-url", name="encode_url")
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
     * @Route("/decode-url", name="decode_url")
     */
    public function decodeUrl(Request $request): JsonResponse
    {
        /** @var UrlRepository $urlRepository */
        $urlRepository = $this->getDoctrine()->getRepository(Url::class);
        $url = $urlRepository->findOneByHash($request->get('hash'));
        if (empty ($url)) {
            return $this->json([
                'error' => 'Non-existent hash.'
            ]);
        }
        return $this->json([
            'url' => $url->getUrl()
        ]);
    }

    /**
     * @Route("/redirect/{hash}", name="redirect_url")
     */
    public function redirectUrl($hash, UrlRepository $urlRepository): RedirectResponse
    {
        $url = $urlRepository->findOneByHash($hash);

        if (!$url) {
            throw $this->createNotFoundException('URL not found');
        }

        return $this->redirect($url->getUrl());
    }
}
