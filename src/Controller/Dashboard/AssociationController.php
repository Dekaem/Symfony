<?php

namespace App\Controller\Dashboard;

use App\Entity\Association;
use App\Form\AssociationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/dashboard/association")
 */
class AssociationController extends AbstractController
{
    private $associationRepository;
    private $entityManager;
    private $userRepository;

    public function __construct(
        AssociationRepository $associationRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->associationRepository = $associationRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }
    /**
     * @Route("/", name="association_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('dashboard/association/index.html.twig', [
            'associations' => $this->associationRepository->findAll(),
            'allUsers' => $this->userRepository->findByAssociationNotNull(),
        ]);
    }

    /**
     * @Route("/new", name="association_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $association = new Association();
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $association->setTotalMembers();
            $this->entityManager->persist($association);
            $this->entityManager->flush();

            return $this->redirectToRoute('association_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/association/new.html.twig', [
            'association' => $association,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="association_show", methods={"GET"})
     */
    public function show(Association $association): Response
    {
        return $this->render('dashboard/association/show.html.twig', [
            'association' => $association,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="association_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Association $association): Response
    {
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $association->setTotalMembers();
            $this->entityManager->flush();

            return $this->redirectToRoute('association_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/association/edit.html.twig', [
            'association' => $association,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="association_delete", methods={"POST"})
     */
    public function delete(Request $request, Association $association): Response
    {
        if ($this->isCsrfTokenValid('delete'.$association->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($association);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('association_index', [], Response::HTTP_SEE_OTHER);
    }
}
