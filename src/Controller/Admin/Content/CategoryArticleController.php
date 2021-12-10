<?php

namespace App\Controller\Admin\Content;

use App\Entity\Contents\Article;
use App\Entity\Contents\ArticleCategory;
use App\Form\Content\ArticleCategoryType;
use App\Interfaces\Datatable\DatatableFieldInterface;
use App\Interfaces\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/article-category", name="admin_article_category_")
 *
 * @Security("is_granted('ADMIN_ARTICLECATEGORY_ACCESS')")
 */
class CategoryArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private SluggerInterface $slugger;

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
     * @param DatatableFieldInterface $datatableField
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,
                          DatatableFieldInterface $datatableField): Response
    {
        $table = $dataTableFactory->create();

        $datatableField
            ->addFieldWithEditField($table, 'title',
                'Titre de la catégorie',
                'article-category',
                'ADMIN_ARTICLECATEGORY_EDIT'
            );

        $datatableField
            ->addClinicField($table);

        $datatableField
            ->addCreatedBy($table)
            ->addDeleteField($table, 'admin/content/category/include/_delete-button.html.twig', [
                'entity'         => 'category',
                'authorizations' => 'ADMIN_ARTICLECATEGORY_DELETE'
            ])
            ->addOrderBy('title')
        ;

        $datatableField->createDatatableAdapter($table, ArticleCategory::class);

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
     * @Security("is_granted('ADMIN_ARTICLECATEGORY_ADD')")
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
     * @Security("is_granted('ADMIN_ARTICLECATEGORY_EDIT', category)")
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
     * @Security("is_granted('ADMIN_ARTICLECATEGORY_DELETE', category)")
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