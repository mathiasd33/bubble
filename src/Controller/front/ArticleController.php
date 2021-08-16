<?php


namespace App\Controller\front;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends AbstractController
{

    /**
     * @Route("/articles", name="front_articles_list")
     */
    public function ArticlesList(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();
        return $this->render('front/articles_list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route ("/article/{id}", name="front_article_show")
     */

    public function article_show($id, ArticleRepository $articleRepository)
    {
        $article = $articleRepository->find($id);
        if (is_null($article)){
            throw new NotFoundHttpException();
        }
        return $this->render("front/article_show.html.twig", [
            'article' => $article
        ]);
    }




}