<?php

namespace App\Controller\Dashboard;

use App\Entity\User;
use App\Entity\TableRonde;
use App\Repository\UserRepository;
use App\Repository\TableRondeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/dashboard/distribution")
 */
class DistributionController extends AbstractController
{
    private $manager;
    private $userRepository;
    private $tableRondeRepository;

    public function __construct(
        EntityManagerInterface $manager,
        UserRepository $userRepository,
        TableRondeRepository $tableRondeRepository
    ) {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->tableRondeRepository = $tableRondeRepository;
    }

    /**
     * @Route("/", name="distribution")
     */
    public function index(): Response
    {

        $allParticipants = $this->userRepository->findAll();
        $participants = $this->userRepository->findByAssociationNotNull();
        $participantsNoTable = array();
        foreach ($participants as $participant) {
            if (count($participant->getTableRondes()) == 0) {
                $participantsNoTable[] = $participant;
            }
        }

        $tableRondes = $this->tableRondeRepository->findAll();

        // Variables de configuration des tables
        $nbMaxPerTable = 8;
        $nbTables = (int)(count($participants) / $nbMaxPerTable) + (count($participants) % $nbMaxPerTable > 0 ? 1 : 0);

        return $this->render('dashboard/distribution/index.html.twig', [
            'tablesRondes' => $tableRondes,
            'nbTables' => $nbTables,
            'nbParticipants' => count($participants),
            'lastTable' => count($participants) % $nbMaxPerTable,
            'participantsNoTable' => $participantsNoTable
        ]);
    }
}
