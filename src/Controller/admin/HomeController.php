<?php


namespace App\Controller\admin;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/admin", name="admin_home")
     */
    public function home (){


        return $this->render("admin/home.html.twig");
    }
}