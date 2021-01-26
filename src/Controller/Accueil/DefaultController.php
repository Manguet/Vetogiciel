<?php

namespace App\Controller\Accueil;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/accueil/", name="admin_accueil_")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig');
    }
}