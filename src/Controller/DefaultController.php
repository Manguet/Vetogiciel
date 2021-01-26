<?php

namespace App\Controller;

use App\Entity\Contents\Article;
use App\Entity\Structure\Sector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class DefaultController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        $sectors = $this->getSectorsByFour();

        $articles = $this->entityManager->getRepository(Article::class)->findByMax(4);

        return $this->render('index.html.twig', [
            'sectors'  => $sectors,
            'articles' => $articles,
        ]);
    }

    /**
     * @return array of sectors grouped by 4
     */
    private function getSectorsByFour(): array
    {
        $sectors = $this->entityManager->getRepository(Sector::class)->findAll();

        $sectorsByThree = [];
        $i = $j = 0;
        foreach ($sectors as $sector) {
            if ($i % 4 === 0) {
                $j++;
            }
            $sectorsByThree[$j][] = $sector;

            $i++;
        }

        return $sectorsByThree;
    }
}
