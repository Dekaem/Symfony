<?php

namespace App\Controller\Dashboard;

use App\Entity\TableRonde;
use App\Form\TableRondeType;
use App\Repository\TableRondeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard/table-ronde")
 */
class TableRondeController extends AbstractController
{
    /**
     * @Route("/", name="table_ronde_index", methods={"GET"})
     */
    public function index(TableRondeRepository $tableRondeRepository): Response
    {
        return $this->render('dashboard/table_ronde/index.html.twig', [
            'table_rondes' => $tableRondeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="table_ronde_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tableRonde = new TableRonde();
        $form = $this->createForm(TableRondeType::class, $tableRonde);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tableRonde);
            $entityManager->flush();

            return $this->redirectToRoute('table_ronde_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/table_ronde/new.html.twig', [
            'table_ronde' => $tableRonde,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="table_ronde_show", methods={"GET"})
     */
    public function show(TableRonde $tableRonde): Response
    {
        return $this->render('dashboard/table_ronde/show.html.twig', [
            'table_ronde' => $tableRonde,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="table_ronde_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TableRonde $tableRonde, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TableRondeType::class, $tableRonde);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('table_ronde_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/table_ronde/edit.html.twig', [
            'table_ronde' => $tableRonde,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="table_ronde_delete", methods={"POST"})
     */
    public function delete(Request $request, TableRonde $tableRonde, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tableRonde->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tableRonde);
            $entityManager->flush();
        }

        return $this->redirectToRoute('table_ronde_index', [], Response::HTTP_SEE_OTHER);
    }
}
