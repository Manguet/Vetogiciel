<?php

namespace App\Controller\Site\Articles;

use App\Entity\Contents\ArticleCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/articles", name="articles_")
 */
class CategoryArticleController extends AbstractController
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
        $categories = $this->entityManager->getRepository(ArticleCategory::class)
            ->findAll();

        return $this->render('site/article/category_articles/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     *
     * @param null|ArticleCategory $articleCategory
     *
     * @return Response
     */
    public function show(?ArticleCategory $articleCategory): Response
    {
        if (null === $articleCategory) {
            throw $this->createNotFoundException('404');
        }

        $categories = $this->entityManager->getRepository(ArticleCategory::class)
            ->findAll();

        return $this->render('site/article/category_articles/show.html.twig', [
            'category'   => $articleCategory,
            'categories' => $categories,
        ]);
    }
}