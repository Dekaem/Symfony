<?php

namespace App\Controller\Pages;

use App\Entity\User;
use App\Form\NewPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppController extends AbstractController
{
    private $entityManager;
    private $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher

    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }

     /**
     * @Route("/user/{id}/new/password/", name="user_new_password", methods={"GET", "POST"})
     */
    public function newPassword(Request $request, User $user): Response
    {

        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Vérification cohérence des mots de passe
                // encode the plain password
                $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('dashboard/user/new_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
