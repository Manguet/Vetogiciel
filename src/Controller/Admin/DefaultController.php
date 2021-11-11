<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Header;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ComboChart;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

    /**
     * @return Response
     */
    public function headerAction(): Response
    {
        $headers = $this->entityManager->getRepository(Header::class)
            ->findBy(['isMainHeader' => false]);

        $response = new Response();

        $content = $this->renderView('admin/manage/_general_nav.html.twig', [
            'headers' => $headers
        ]);

        $response->setContent($content);

        return $response;
    }
}