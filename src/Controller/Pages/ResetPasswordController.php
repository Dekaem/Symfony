<?php

namespace App\Controller\Pages;

use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{

     /**
     * @Route("/{id}/reset/password/", name="user_reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Vérification cohérence des mots de passe
                // encode the plain password
                $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();
            

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('user/reset_passwd.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}

