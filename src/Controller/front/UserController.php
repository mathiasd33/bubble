<?php


namespace App\Controller\front;


use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/front/users", name="front_users_list")
     */
    public function usersArticlesList(UserRepository $userRepository)
    {

        $users = $userRepository->findAll();

        return $this->render('front/users_list.html.twig', [
            'users' => $users

        ]);
    }

    /**
     * @Route ("/front/user/{id}", name="front_user_show")
     */
    public function user_show($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        if (is_null($user)){
            throw new NotFoundHttpException();
        }

        return $this->render("front/user_show.html.twig", [
            'user' => $user
        ]);
    }

    /**
     * @Route ("/front/insert/user", name="front_insert_user")
     */
    public function insertUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger){

        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $imageFile = $userForm->get('image')->getData();

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

                $user->setImage($newFilename);
            }
        }

        if ($userForm->isSubmitted() && $userForm->isValid()){
            $user->setRoles(["ROLE_USER"]);

            $plainPassword = $userForm->get('password')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user,$plainPassword);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

        }

        return $this->render('front/insertUser.html.twig',[
            'userForm' => $userForm->createView()
        ]);
    }

    /**
     * @Route ("/front/articlesUser/{id}", name="articles_user")
     */
    public function articlesUser($id, UserRepository $userRepository){
       $user=$userRepository->find($id);
       return $this->render("front/articles_user.html.twig",[
           'user' =>$user
       ]);

    }
}