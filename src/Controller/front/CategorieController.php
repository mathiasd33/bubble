<?php

namespace App\Controller\front;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{


    /**
     * @Route ("/front/categories", name="categories")
     */

    public function categories_list(CategorieRepository $categorieRepository)
    {
        $categories = $categorieRepository->findAll();
        return $this->render('base.html.twig',[
            'categories' =>$categories
        ]);
    }



    /**
     * @Route ("/front/categorie/{id}", name="categorie_show")
     */

    public function categorie_show($id, CategorieRepository $categorieRepository)
    {
        $categorie = $categorieRepository->find($id);
        if (is_null($categorie)){
            throw new NotFoundHttpException();
        }
        return $this->render("front/categorie_show.html.twig", [
            'categorie' => $categorie
        ]);
    }
}