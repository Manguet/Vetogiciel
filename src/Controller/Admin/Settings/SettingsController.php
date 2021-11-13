<?php

namespace App\Controller\Admin\Settings;

use App\Entity\Settings\Configuration;
use App\Form\Settings\ConfigurationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/settings/", name="settings_")
 */
class SettingsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SettingsController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route ("index", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('admin/settings/index.html.twig');
    }

    /**
     * @Route ("configuration/{type}", name="configuration")
     *
     * @param Request $request
     * @param string $type
     *
     * @return Response
     */
    public function configurations(Request $request, string $type): Response
    {
        $configurations = $this->entityManager->getRepository(Configuration::class)
            ->findByConfigurationInOrder($type);

        $form = $this->createForm(ConfigurationType::class, null, [
            'configurations' => $configurations
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($request->get('configuration') as $configurationName => $configurationData) {

                $configuration = $this->entityManager->getRepository(Configuration::class)
                    ->findOneBy(['name' => $configurationName]);

                if ($configuration) {

                    $configuration->setDatas(['values' => $configurationData]);
                    $this->entityManager->persist($configuration);
                }
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('settings_configuration', [
                'type' => $type
            ]);
        }

        $onglets = [];
        foreach ($configurations as $configuration) {
            $onglets[$configuration->getOnglet()][] = $configuration->getName();
        }

        return $this->render('admin/settings/configurations/index.html.twig', [
            'form'           => $form->createView(),
            'onglets'        => $onglets,
            'type'           => $type,
        ]);
    }
}