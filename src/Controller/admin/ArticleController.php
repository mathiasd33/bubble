<?php


namespace App\Controller\admin;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/articles", name="admin_articles_list")
     */
    public function adminArticlesList(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();
        return $this->render('admin/articles_list.html.twig', [
            'articles' => $articles
        ]);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/admin/article/insert",name="admin_article_insert")
     */
    public function insertArticle(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {

        $article = new Article();
        //on génère le formulaire en utilisant le gabarit + une instance de l entité Article
        $articleForm = $this->createForm(ArticleType::class, $article);

        // on lie le formulaire aux données de POST
        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $imageFile = $articleForm->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '_' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $exception) {
                    // ... handle exception if something happens during file uploads
                }

                $article->setImage($newFilename);
            }

                $this->addFlash(
                    'succes',
                    'Votre article ' . $article->getTitle() . ' à bien été crée !'
                );
                $entityManager->persist($article);
                $entityManager->flush();

                return $this->redirectToRoute('admin_articles_list');

        }
        return $this->render('admin/insertArticle.html.twig', [
        'articleForm' => $articleForm->createView()
    ]);

    }


        /**
         * @IsGranted("ROLE_ADMIN")
         * @Route ("/admin/article/update/{id}",name="admin_article_update")
         */
        public function updateArticle($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger)
    {

        $article = $articleRepository->find($id);

        //on génère le formulaire en utilisant le gabarit + une instance de l entité Article
        $articleForm = $this->createForm(ArticleType::class, $article);

        // on lie le formulaire aux données de POST
        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $imageFile = $articleForm->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '_' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $exception) {
                    // ... handle exception if something happens during file uploads
                }

                $article->setImage($newFilename);
            }

            $this->addFlash(
                'succes',
                'Votre article ' . $article->getTitle() . ' à bien été modifiée !'
            );
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('admin_articles_list');

        }

        return $this->render('admin/insertArticle.html.twig', [
            'articleForm' => $articleForm->createView()
        ]);

    }

        /**
         * @IsGranted("ROLE_ADMIN")
         * @Route ("/admin/article/delete/{id}",name="admin_article_delete")
         */
        public
        function deleteArticle($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager)
        {
            $article = $articleRepository->find($id);

            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash(
                'succes',
                'Votre article ' . $article->getTitle() . ' à bien été supprimée !'
            );

            return $this->redirectToRoute('admin_articles_list');
        }


}