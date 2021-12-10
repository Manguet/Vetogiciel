<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Header;
use App\Entity\Patients\Client;
use App\Entity\Settings\Role;
use App\Entity\Structure\Clinic;
use App\Interfaces\Charts\ChartCreationInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\AreaChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ColumnChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Security("is_granted('ADMIN_ADMIN_ACCESS')")
 */
class DefaultController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private RequestStack $requestStack;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestStack $requestStack
     */
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack  = $requestStack;
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
     * @param ChartCreationInterface $chartCreation
     *
     * @return Response
     */
    public function manage(ChartCreationInterface $chartCreation): Response
    {
        $chart = $chartCreation->createChart(
            new AreaChart(),
            ['Jour', 'Ce jour'],
            range(1, 24),
            [0,0,0,0,0,0,0,0,120,210,350,460,460,460,580,690,850,980,980,980,980,980,980,980],
            [504, 831],
            'Evolution du CA'
        );

        $secondChart = $chartCreation->createChart(
            new ColumnChart(),
            ['Utilisateurs', 'Ce jour'],
            range(1,24, 4),
            [10, 16, 5, 9, 10, 14],
            [150, 275],
            'Nouveaux Clients aujourd\'hui'
        );

        $thirdChart = $chartCreation->createChart(
            new LineChart(),
            ['Durée moyenne en consultation', 'Ce jour'],
            range(1, 24, 4),
            [10, 20, 16, 12, 14, 8],
            [150, 275],
            'Durée moyenne en consultation'
        );

        $fourthChart = $chartCreation->createChart(
            new BarChart(),
            ['Animaux hospitalisés', 'Ce jour'],
            range(1,24,4),
            [2, 4, 3, 4, 3, 2],
            [150, 275],
            'Animaux hospitalisés'
        );

        $fifthChart = $chartCreation->createChart(
            new AreaChart(),
            ['Panier moyen', 'Ce jour'],
            range(1,24,4),
            [250, 490, 300, 130, 50, 200],
            [150, 275],
            'Panier moyen'
        );

        $clinics = $this->entityManager->getRepository(Clinic::class)->findAll();

        $clients = $this->entityManager->getRepository(Client::class)->findBy([], [], 4);

        if ($this->getUser()) {
            $role = $this->getUser()->getRoles();

            $role = $this->entityManager->getRepository(Role::class)
                ->findOneBy(['name' => $role]);

            if ($role) {
                $authorizationLevel = $role->getPermissionLevel();
            }
        }


        return $this->render('admin/manage/manage.html.twig', [
            'chart'              => $chart,
            'secondChart'        => $secondChart,
            'thirdChart'         => $thirdChart,
            'fourthChart'        => $fourthChart,
            'fifthChart'         => $fifthChart,
            'clinics'            => $clinics,
            'clients'            => $clients,
            'authorizationLevel' => $authorizationLevel ?? null
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

        $masterRequest = $this->requestStack->getMasterRequest();

        if ($masterRequest) {
            $route = $masterRequest->attributes->get('_route');
        }

        $content = $this->renderView('admin/manage/_general_nav.html.twig', [
            'headers'  => $headers,
            'route'    => $route ?? null,
        ]);

        $response->setContent($content);

        return $response;
    }
}