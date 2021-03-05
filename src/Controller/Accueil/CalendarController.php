<?php

namespace App\Controller\Accueil;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/calendar/", name="admin_calendar_")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class CalendarController extends AbstractController
{
    /**
     * @Route("index", name="index")
     */
    public function index(): Response
    {
        return $this->render('accueil/calendar/index.html.twig');
    }

}