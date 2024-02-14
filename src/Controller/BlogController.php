<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\AddType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
    }

    #[Route('/blog', name: 'blog')]
    public function index(): Response
    {
        $articles = $this->articleRepository->findAll();
        return $this->render('blog/index.html.twig', ['articles' => $articles]);
    }

    #[Route('/blog/add', name: 'blog_add_get', methods: ['GET'])]
    public function get(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(AddType::class, $article);
        return $this->render('blog/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/blog/add', name: 'blog_add_post', methods: ['POST'])]
    public function post(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(AddType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('blog');
        }

        return $this->render('blog/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/blog/{id}', name: 'app_blog_id')]
    public function dynamic(Article $article): Response
    {
        return $this->render('blog/article.html.twig', ['article' => $article]);
    }
}