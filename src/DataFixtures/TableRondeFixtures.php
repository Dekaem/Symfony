<?php

namespace App\DataFixtures;

use App\Entity\Round;
use App\Entity\Table;
use App\Entity\TableRonde;
use App\Repository\UserRepository;
use App\Repository\RoundRepository;
use App\Repository\TableRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\TableRondeRepository;
use App\Repository\AssociationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TableRondeFixtures extends Fixture implements FixtureGroupInterface
{
    private $userRepository;
    private $tableRondeRepository;
    private $associationRepository;

    public function __construct(
        UserRepository $userRepository,
        TableRondeRepository $tableRondeRepository,
        AssociationRepository $associationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->tableRondeRepository = $tableRondeRepository;
        $this->associationRepository = $associationRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $allParticipants = $this->userRepository->findAll();
        // Récupération des participants => Trouver un moyen de les ranger par taille d'association
        $participants = $this->userRepository->findByAssociationNotNull();
        $tableRondes = $this->tableRondeRepository->findAll();
        $tables = $this->TableRepository->findAll();
        $rounds = $this->RoundRepository->findAll();

        foreach ($this->associationRepository->findAll() as $item) {
            $item->setTotalMembers();
            $manager->flush();
        }

        // On supprime les tables et les tours
        foreach ($tables as $table) {
            $manager->remove($table);
            $manager->flush();
        }
        foreach ($rounds as $round) {
            $manager->remove($round);
            $manager->flush();
        }

        // On supprime les tables rondes
        foreach ($tableRondes as $tableRonde) {
            $manager->remove($tableRonde);
            $manager->flush();
        }


        // Variables de configuration des tables
        $nbMaxPerTable = 8;
        $nbTables = (int)(count($participants) / $nbMaxPerTable) + (count($participants) % $nbMaxPerTable > 0 ? 1 : 0);
        $nbRounds = 6;
        $roundActuel = 1;

        // On créer les tours
        for ($i = 0; $i < $nbRounds; $i++) {
            $round = new Round();
            $manager->persist($round);
        }
        
        $manager->flush();

         // On créer les tables
         for ($i = 0; $i < $nbTables; $i++) {
            $table = new Table();
            $manager->persist($table);
        }
        
        $manager->flush();



        // On créer les tables rondes pour le 1er tour
        for ($i = 1; $i <= $nbTables; $i++) {
            $table = new Table();
            $manager->persist($table);
            for ($j = 0; $j < $nbMaxPerTable; $j++) {
                $tableRonde = new TableRonde();
                $tableRonde->setTableNumber($table);
                $tableRonde->setRoundNumber($roundActuel);

                $manager->persist($tableRonde);
            }
        }

        $manager->flush(); 

        // Création des tableaux des tables rondes
        $i = 1;
        foreach ($tableRondes as $tableRonde) {
            ${'table' . $i} = array();
            ${'tableAssociation' . $i} = array();
            $i++;
        }

        $associations =  $this->associationRepository->findByMembers();

        /*
        foreach ($associations as $association) {
            // On boucle sur les participants
            foreach ($association->getUsers() as $participant) {
                var_dump('Utilisateur ' . $participant->getId());
                foreach ($tableRondes as $tableRonde) {
                    var_dump('Table ' . $tableRonde->getTableNumber());
                    $numeroTable = $tableRonde->getTableNumber(); // On récupère le numéro de la table
                    $tableRonde = $this->tableRondeRepository->findByTableNumber($numeroTable); // On récupère la table
                    if (count(${'table' . $numeroTable}) == $nbMaxPerTable) {
                        var_dump('La table ' . $numeroTable . ' est pleine');
                        continue;
                    }
                    if (!in_array($participant, ${'table' . $numeroTable}) && count(${'table' . $numeroTable}) < $nbMaxPerTable && !in_array($participant->getAssociation(), ${'tableAssociation' . $numeroTable}) && $tableRonde->getUsers() === null) {

                        $tableRonde->setUsers($participant);
                        ${'table' . $numeroTable}[] = $participant;
                        ${'tableAssociation' . $numeroTable}[] = $participant->getAssociation();

                        $manager->persist($tableRonde);

                        var_dump('Utilisateur ajouté à la table ' . $tableRonde->getTableNumber());
                        break;
                    }

                    $manager->flush();
                }
            }
        }*/


        foreach ($associations as $association) {
            // On boucle sur les participants
            foreach ($association->getUsers() as $participant) {
                var_dump('Utilisateur ' . $participant->getId());
                foreach ($tableRondes as $tableRonde) {
                    var_dump('Table ' . $tableRonde->getTableNumber());
                    $numeroTable = $tableRonde->getTableNumber(); // On récupère le numéro de la table
                    $tableRonde = $this->tableRondeRepository->findByTableNumber($numeroTable); // On récupère la table

                    if (count(${'table' . $numeroTable}) == $nbMaxPerTable) {
                        var_dump('La table ' . $numeroTable . ' est pleine');
                        continue;
                    }
                    if (!in_array($participant, ${'table' . $numeroTable}) && count(${'table' . $numeroTable}) < $nbMaxPerTable && !in_array($participant->getAssociation(), ${'tableAssociation' . $numeroTable}) && $tableRonde->getUsers() === null) {

                        $tableRonde->setUsers($participant);
                        ${'table' . $numeroTable}['personne'.$j] = $participant;
                        ${'tableAssociation' . $numeroTable}[] = $participant->getAssociation();

                        $manager->persist($tableRonde);

                        var_dump('Utilisateur ajouté à la table ' . $tableRonde->getTableNumber());
                        break;
                    }

                    $manager->flush();
                }
            }
        }


        // Reste participants
        $resteParticipants = [];
        $allUsers = $this->userRepository->findAll();
        foreach ($allUsers as $singleUser) {
            if (!$this->tableRondeRepository->findByUserAndRound($singleUser, 1) ) {
                $resteParticipants[] = $singleUser;
            }
        }

        foreach ($associations as $association) {
            // On boucle sur les participants restants
            foreach ($resteParticipants as $participant) {
                var_dump('Utilisateur ' . $participant->getId());
                foreach ($tableRondes as $tableRonde) {

                    var_dump('Table ' . $tableRonde->getTableNumber());
                    $numeroTable = $tableRonde->getTableNumber(); // On récupère le numéro de la table
                    $table = $this->tableRondeRepository->findByTableNumber($numeroTable); // On récupère la table
                    if (count(${'table' . $numeroTable}) == $nbMaxPerTable) {
                        var_dump('La table ' . $numeroTable . ' est pleine');
                        continue;
                    }
                    if (!in_array($participant, ${'table' . $numeroTable}) && count(${'table' . $numeroTable}) < $nbMaxPerTable && $tableRonde->getUsers() === null) {

                        $tableRonde->setUsers($participant);
                        ${'table' . $numeroTable}[] = $participant;

                        // On pourra voir ici pour mettre un nombre maximum de membres de cette association sur une table (en fonction du pourcentage du total des membres)
                        // ${'tableAssociation' . $numeroTable}[] = $participant->getAssociation();

                        $manager->persist($tableRonde);

                        var_dump('Utilisateur ajouté à la table ' . $tableRonde->getTableNumber());
                    }

                    $manager->flush();
                }
            }
        }
    }

    public static function getGroups(): array
    {
        return ['group2'];
    }
}
