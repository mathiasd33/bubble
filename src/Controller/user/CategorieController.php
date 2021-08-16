<?php

namespace App\Controller\user;

use App\Repository\CategorieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{


    /**
     * @IsGranted("ROLE_USER")
     * @Route ("/user/categories", name="user_categories")
     */

    public function categories_list(CategorieRepository $categorieRepository)
    {
        $categories = $categorieRepository->findAll();
        return $this->render('base.html.twig',[
            'categories' =>$categories
        ]);
    }



    /**
     * @Route ("/user/categorie/{id}", name="user_categorie_show")
     */

    public function categorie_show($id, CategorieRepository $categorieRepository)
    {
        $categorie = $categorieRepository->find($id);
        if (is_null($categorie)){
            throw new NotFoundHttpException();
        }
        return $this->render("user/categorie_show.html.twig", [
            'categorie' => $categorie
        ]);
    }
}