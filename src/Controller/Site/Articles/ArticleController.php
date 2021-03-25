<?php

namespace App\Controller\Site\Articles;

use App\Entity\Contents\Article;
use App\Entity\Contents\ArticleCategory;
use App\Entity\Contents\Commentary;
use App\Form\Content\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/article", name="article_")
 */
class ArticleController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * ArticleController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     * @param PaginatorInterface $paginator
     */
    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag,
                                PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->flashBag      = $flashBag;
        $this->paginator     = $paginator;
    }

    /**
     * @Route("/{id}", name="show")
     *
     * @param Request $request
     * @param Article|null $article
     *
     * @return Response
     */
    public function show(Request $request, ?Article $article): Response
    {
        if (!$article) {
            throw $this->createNotFoundException('404');
        }

        $category = $article->getArticleCategory();

        if ($category) {

            $articles = $this->entityManager->getRepository(Article::class)
                ->findOthersByCategory($category, $article->getId());
        }

        $categories = $this->entityManager->getRepository(ArticleCategory::class)
            ->findAll();

        $allComments = $this->entityManager->getRepository(Commentary::class)
            ->findBy(['article' => $article], ['dateCreation' => 'DESC']);

        $comments = $this->paginator->paginate(
            $allComments,
            $request->query->getInt('page', 1),
            4
        );

        $commentary = new Commentary();

        $form = $this->createForm(CommentType::class, $commentary);

        $form->handleRequest($request);

        return $this->render('site/article/show.html.twig', [
            'article'       => $article,
            'otherArticles' => $articles ?? null,
            'comments'      => $comments,
            'form'          => $form->createView(),
            'categories'    => $categories,
        ]);
    }

    /**
     * @Route("/comment/user", name="comment")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function postComment(Request $request): RedirectResponse
    {
        $articleId = $request->query->get('id');

        $article = $this->entityManager->getRepository(Article::class)
            ->find((int)$articleId);

        if (null !== $article && $this->getUser()) {


            $createdBy = $this->getUser()->getLastName() . ' ' . $this->getUser()->getFirstName();

            $canPost = $this->checkPostAvailableServices($createdBy, $article);

            if (!$canPost) {

                $this->addFlash('warning', 'Vous devez attendre avant de pouvoir reposter sur cet article.');

                return $this->redirectToRoute('index');
            }
            $comment = $request->request->get('comment');

            $commentary = new Commentary();
            $commentary->setCreatedBy($createdBy);
            $commentary->setDescription($comment['description']);

            $commentary->setArticle($article);

            $this->entityManager->persist($commentary);
            $this->entityManager->flush();

            return $this->redirectToRoute('article_show', [
                'id'        => (int)$articleId,
                '_fragment' => 'comment-zone',
            ]);

        }

        $this->flashBag->add('warning', 'Vous devez être connecté pour posté');

        return $this->redirectToRoute('index');
    }

    /**
     * @param string $createdBy
     * @param Article $article
     *
     * @return bool
     */
    private function checkPostAvailableServices(string $createdBy, Article $article): bool
    {
        $comments = $this->entityManager->getRepository(Commentary::class)
            ->findLastHourCommentary($createdBy, $article);

        if (empty($comments)) {

            return true;
        }

        return false;
    }
}