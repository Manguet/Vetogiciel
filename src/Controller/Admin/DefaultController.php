<?php

namespace App\Controller\Admin;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ComboChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security_login');
        }

        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/manage", name="manage")
     *
     * @return Response
     */
    public function manage(): Response
    {
        // TODO make service for graph and datas
        $chart = new ComboChart();
        $chart->getData()->setArrayToDataTable(
            [['Année', '2018', '2019', '2020'],
                ['janv', 0, 0, 0],
                ['juin', 0, 7, 18],
                ['dec' , 4, 13, 24],
            ]
        );

        $chart->getOptions()->setTitle('Evolution du CA');
        $chart->getOptions()->setHeight(600);
        $chart->getOptions()->setWidth(900);
        $chart->getOptions()->getTitleTextStyle()->setBold(true);
        $chart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $chart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $chart->getOptions()->getTitleTextStyle()->setFontSize(20);

        return $this->render('admin/manage/manage.html.twig', [
            'chart' => $chart,
        ]);
    }

}