<?php


namespace App\Controller\front;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route ("/", name="home")
     */

    public function home(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();
        return $this->render("front/home.html.twig", ['articles' => $articles]);
    }
}