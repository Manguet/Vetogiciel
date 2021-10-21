<?php

namespace App\Controller\Admin\Content;

use App\Entity\Contents\Article;
use App\Entity\Contents\ArticleCategory;
use App\Form\Content\ArticleCategoryType;
use App\Interfaces\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/article-category", name="admin_article_category_")
 */
class CategoryArticleController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * CategoryArticleController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     */
    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger       = $slugger;
    }

    /**
     * @Route("", name="index")
     *
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('title', TextColumn::class, [
                'label'     => 'Titre de la catégorie',
                'orderable' => true,
                'render'    => function ($value, $category) {
                    return '<a href="/admin/article-category/edit/' . $category->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('delete', TextColumn::class, [
                'label'  => 'Supprimer ?',
                'render' => function ($value, $category) {
                    return $this->renderView('admin/content/category/include/_delete-button.html.twig', [
                        'category' => $category,
                    ]);
                }
            ])
            ->addOrderBy('title')
            ->createAdapter(ORMAdapter::class, [
                'entity' => ArticleCategory::class
            ]);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/content/category/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newCategory(Request $request): Response
    {
        $category = new ArticleCategory();

        $form = $this->createForm(ArticleCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setTitleUrl(
                $this->slugger->generateSlugUrl(
                    $category->getTitle(), ArticleCategory::class
                )
            );

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_article_category_index');
        }

        return $this->render('admin/content/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     *
     * @param ArticleCategory $category
     * @param Request $request
     *
     * @return Response
     */
    public function edit(ArticleCategory $category, Request $request): Response
    {
        $form = $this->createForm(ArticleCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_article_category_index');
        }

        return $this->render('admin/content/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param ArticleCategory $category
     *
     * @return JsonResponse
     */
    public function delete(ArticleCategory $category): JsonResponse
    {
        if (!$category instanceof ArticleCategory) {
            return new JsonResponse('Category Not Found', 404);
        }

        $articles = $this->entityManager->getRepository(Article::class)
            ->findByCategory($category);

        foreach ($articles as $article) {
            $article->setArticleCategory(null);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return new JsonResponse('Category deleted with success', 200);
    }
}