<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\EntityType;
//use App\Controller\CommentType;
use App\Form\ArticleType;

use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use MercurySeries\FlashyBundle\MercurySeriesFlashyBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BlogController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }



    /**
     * @Route("/", name="blog")
     */
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate(
            $articleRepository->findAll(),
            $request->query->getInt('page', 1),
            2
        );
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/new", name="article_new")
     */

    public function new(Request $request, FlashyNotifier $flashyNotifer)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new DateTime());
            $article->setImage("https://picsum.photos/seed/picsum/300/150");
            $article->setUser($this->security->getUser()); #user 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $flashyNotifer->success('Article created!');
            return $this->redirectToRoute("article_show", ['id' => $article->getId()]);
        }
        return $this->render('blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}/edit", name="article_edit")
     */

    public function edit(Request $request, Article $article,  FlashyNotifier $flashyNotifer): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $flashyNotifer->success('Article Modifié!');

            return $this->redirectToRoute("article_show", ['id' => $article->getId()]);
        }



        return $this->render('blog/edit.html.twig', [
            'editform' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show", methods={"GET", "POST"})
     */

    public function show(Article $article, Request $request,  FlashyNotifier $flashyNotifer)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setArticle($article);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $flashyNotifer->success('Commentaire ajouté!');
            return $this->redirectToRoute("article_show", ['id' => $article->getId()]);
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
