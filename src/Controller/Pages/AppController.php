<?php

namespace App\Controller\Pages;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $associationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        AssociationRepository $associationRepository

    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->associationRepository = $associationRepository;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }

    /**
     * @Route("/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard(): Response
    {
        return $this->render('dashboard/dashboard.html.twig', [
            'users' => $this->userRepository->findByAssociationNotNull(),
            'associations' => $this->associationRepository->findByMembers(),
        ]);
    }
}
