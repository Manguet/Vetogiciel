<?php

namespace App\Controller\Admin\Content;

use App\Entity\Contents\Article;
use App\Entity\Structure\Veterinary;
use App\Form\Content\ArticleType;
use App\Service\Priority\PriorityServices;
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
 * @Route("/admin/content", name="admin_content_")
 */
class ArticleController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PriorityServices
     */
    private $priorityServices;

    /**
     * ArticleController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PriorityServices $priorityServices
     */
    public function __construct(EntityManagerInterface $entityManager, PriorityServices $priorityServices)
    {
        $this->entityManager    = $entityManager;
        $this->priorityServices = $priorityServices;
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
                'label'     => 'Titre de l\'article',
                'orderable' => true,
                'render'    => function ($value, $content) {
                    return '<a href="/admin/content/edit/' . $content->getId() . '">' . $value . '</a>';
                }
            ])
            ->add('articleCategory', TextColumn::class, [
                'label'     => 'Catégorie',
                'orderable' => false,
                'render'    => function ($value, $content) {

                    if ($content->getArticleCategory()) {
                        return $content->getArticleCategory()->getTitle();
                    }
                    return '';
                }
            ])
            ->add('isActivated', TextColumn::class, [
                'label'     => 'Article actif ?',
                'orderable' => true,
                'render'    => function ($value, $content) {
                    if ($content->getIsActivated()) {
                        return 'Actif';
                    }
                    return 'Désactivé';
                }
            ])
            ->add('priority', TextColumn::class, [
                'label'     => 'Priorité d\'affichage',
                'orderable' => true,
            ])
            ->add('delete', TextColumn::class, [
                'label'  => 'Supprimer ?',
                'render' => function ($value, $article) {
                    return $this->renderView('admin/content/include/_delete-button.html.twig', [
                        'article' => $article,
                    ]);
                }
            ])
            ->addOrderBy('priority')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Article::class
            ]);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/content/index.html.twig', [
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
    public function newArticle(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article, [
            'article' => null,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $priority = $this->priorityServices->setPriorityOnCreation($article);
            $article->setPriority($priority);

            $user = $this->getUser();

            if ($user instanceof Veterinary) {
                $article->setCreatedBy($user);
            }

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_content_index');
        }

        return $this->render('admin/content/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     *
     * @param Article $article
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Article $article, Request $request): Response
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'article' => $article,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_content_index');
        }

        return $this->render('admin/content/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param Article $article
     *
     * @return JsonResponse
     */
    public function delete(Article $article): JsonResponse
    {
        if (!$article instanceof Article) {
            return new JsonResponse('Article Not Found', 404);
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        $articles = $this->entityManager->getRepository(Article::class)
            ->findByArticlePriority();

        $priority = 0;
        foreach ($articles as $articleAfterUpdate) {
            $articleAfterUpdate->setPriority($priority);
            $priority++;
        }

        $this->entityManager->flush();

        return new JsonResponse('Article deleted with success', 200);
    }

    /**
     * @Route("/priority", name="priority")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function priority(Request $request): Response
    {
        if (($request->getMethod() === 'POST') && ($request->isXmlHttpRequest())) {
            $movedArticles = $request->get('movedArticles');
            $newArticles   = $request->get('newArticles');
            $articles = $articlePositions = [];

            $i = 0;
            if (is_array($movedArticles) && is_array($newArticles)) {

                foreach ($movedArticles as $movedArticle) {
                    $articles[$movedArticle] = $newArticles[$i];
                    $i++;
                }

                foreach ($articles as $oldPosition => $newPosition) {

                    $article = $this->entityManager->getRepository(Article::class)
                        ->findOneBy([
                            'priority' => (int)$oldPosition,
                        ])
                    ;

                    if ($article) {
                        $articlePositions[$article->getId()] = $newPosition;
                    }
                }

                foreach ($articlePositions as $id => $newPosition) {

                    $article = $this->entityManager->getRepository(Article::class)
                        ->find((int)$id)
                    ;

                    $article->setPriority($newPosition);
                    $this->entityManager->persist($article);
                }

                $this->entityManager->flush();

                return new JsonResponse('Priorité mise à jour', 200);
            }
            return new JsonResponse('Pas de modification', 200);

        }

        return new Response('error', 500);
    }
}