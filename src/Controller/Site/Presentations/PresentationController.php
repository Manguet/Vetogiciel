<?php

namespace App\Controller\Site\Presentations;

use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/presentation", name="presentation_")
 */
class PresentationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PresentationController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}/{entity}", name="show")
     *
     * @return Response
     */
    public function show(int $id, $entity = 'veterinary'): Response
    {
        if ($entity === 'veterinary') {

            $worker = $this->entityManager->getRepository(Veterinary::class)
                ->find($id);

        } elseif ($entity === 'employee') {

            $worker = $this->entityManager->getRepository(Employee::class)
                ->find($id);

        } else {

            throw $this->createNotFoundException('404');
        }

        return $this->render('site/presentation/show.html.twig', [
            'worker' => $worker,
        ]);
    }
}