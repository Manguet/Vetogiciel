<?php

namespace App\Service\Site\Annonce;

use App\Entity\Settings\Configuration;
use App\Entity\Structure\Clinic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AnnonceServices
{
    private EntityManagerInterface $entityManager;

    private Environment $environment;

    public function __construct(EntityManagerInterface $entityManager, Environment $environment)
    {
        $this->entityManager = $entityManager;
        $this->environment   = $environment;
    }

    /**
     * @param Response $response
     * @param Clinic|null $clinic
     * @param Request $request
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getAnnoncesContent(Response $response, ?Clinic $clinic, Request $request): Response
    {
        $generalCookieId = $this->getAnnonceReference('annonce_cookie');

        $generalAnnonceContent = $this->getContentAnnonce(
            $generalCookieId,
            $request,
            'site_annonce',
            'annonce'
        );

        $clinicAnnonceContent = null;

        if ($clinic) {
            $clinicAnnonceCookieId = $this->getAnnonceReference('annonce_cookie_' . $clinic->getId());

            $isAnnonce = (bool)$generalAnnonceContent;

            $clinicAnnonceContent = $this->getContentAnnonce(
                $clinicAnnonceCookieId,
                $request,
                'site_annonce_' . $clinic->getId(),
                'annonceClinic',
                $isAnnonce
            );
        }

        $content = $this->getFinalContent($generalAnnonceContent, $clinicAnnonceContent);

        if ($content) {
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * @param string $annonceCookieReference
     *
     * @return null|string
     */
    private function getAnnonceReference(string $annonceCookieReference): ?string
    {
        $annonceCookie = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy(['name' => $annonceCookieReference]);

        if ($annonceCookie && isset($annonceCookie->getDatas()['values'])) {
            $cookieId = $annonceCookie->getDatas()['values'];
        }

        return $cookieId ?? null;
    }

    /**
     * @param string|null $generalCookieId
     * @param Request $request
     * @param string $annonceTitle
     * @param string $title
     * @param bool|null $isAnnonce
     *
     * @return string|null
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getContentAnnonce(?string $generalCookieId, Request $request,
                                       string $annonceTitle, string $title, ?bool $isAnnonce = false): ?string
    {
        $request->getSession()->remove($generalCookieId);

        if ($generalCookieId && !$request->getSession()->get($generalCookieId)) {

            $generalAnnonce = $this->entityManager->getRepository(Configuration::class)
                ->findOneBy(['name' => $annonceTitle]);

            $annonce = $generalAnnonce->getDatas()['values'] ?? null;

            $content = $this->environment->render('base/_annonce.html.twig', [
                $title      => $annonce ?? null,
                'isAnnonce' => $isAnnonce
            ]);

            $request->getSession()->set($generalCookieId, $generalCookieId);
        }

        return $content ?? null;
    }

    /**
     * @param string|null $generalAnnonce
     * @param string|null $clinicAnnonce
     *
     * @return string|null
     */
    private function getFinalContent(?string $generalAnnonce, ?string $clinicAnnonce): ?string
    {
        if (null === $generalAnnonce && null === $clinicAnnonce) {
            return null;
        }

        if (null === $generalAnnonce) {
            return $clinicAnnonce;
        }

        return $generalAnnonce . $clinicAnnonce;
    }
}