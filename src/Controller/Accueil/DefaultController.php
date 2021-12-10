<?php

namespace App\Controller\Accueil;

use App\Service\Authorizations\AdminAuthorizationServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/accueil/", name="admin_accueil_")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DefaultController extends AbstractController
{
    private AdminAuthorizationServices $adminAuthorizationServices;

    /**
     * DefaultController constructor.
     *
     * @param AdminAuthorizationServices $adminAuthorizationServices
     */
    public function __construct(AdminAuthorizationServices $adminAuthorizationServices)
    {
        $this->adminAuthorizationServices = $adminAuthorizationServices;
    }

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        if (!$this->adminAuthorizationServices->testBasicAuthorizationByUser($this->getUser())) {

            throw new AccessDeniedHttpException('Vous n\'avez pas l\'autorisation d\'accéder à cette page');
        }

        return $this->render('accueil/index.html.twig');
    }
}