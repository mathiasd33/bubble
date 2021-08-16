<?php


namespace App\Controller\user;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/user/articles", name="user_articles_list")
     */
    public function userArticlesList(ArticleRepository $articleRepository)
    {
        $user = $this->getUser();
        $articles = $articleRepository->findBy(['user' => $user]);

        return $this->render('user/articles_list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route ("user/article/{id}", name="user_article_show")
     */

    public function article_show($id, ArticleRepository $articleRepository)
    {
        $article = $articleRepository->find($id);
        if (is_null($article)){
            throw new NotFoundHttpException();
        }
        return $this->render("user/article_show.html.twig", [
            'article' => $article
        ]);
    }


    /**
     * @IsGranted("ROLE_USER")
     * @Route ("/user/insert/article",name="user_article_insert")
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

                $user = $this->getUser();
                $article->setUser($user);

                $entityManager->persist($article);
                $entityManager->flush();
                $this->addFlash(
                'succes',
                'Votre article ' . $article->getTitle() . ' à bien été crée !'
            );

                return $this->redirectToRoute('user_articles_list');

        }
        return $this->render('user/insertArticle.html.twig', [
        'articleForm' => $articleForm->createView()
    ]);

    }


        /**
         * @IsGranted("ROLE_USER")
         * @Route ("/user/article/update/{id}",name="user_article_update")
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
            return $this->redirectToRoute('user_articles_list');

        }

        return $this->render('user/insertArticle.html.twig', [
            'articleForm' => $articleForm->createView()
        ]);

    }

        /**
         * @IsGranted("ROLE_USER")
         * @Route ("/user/article/delete/{id}",name="user_article_delete")
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

            return $this->redirectToRoute('user_articles_list');
        }


}