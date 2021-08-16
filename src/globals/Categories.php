<?php


namespace App\globals;


use App\Repository\CategorieRepository;

class Categories
{
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    public function getAll()
    {
        $categories = $this->categorieRepository->findAll();

        return $categories;
    }

}