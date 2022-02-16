<?php

namespace App\DataFixtures;

use App\Entity\TableRonde;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\TableRondeRepository;
use App\Repository\AssociationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TablesRondesFixtures extends Fixture implements FixtureGroupInterface
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

        function in_array_once($value, $tab, $number) {
            foreach ($tab as $key) {
                $new_tab[] = $key->getName();
            }
            $tab_doublon = array_count_values($new_tab);
            var_dump($new_tab);
            var_dump($tab_doublon);
            echo "Membre de l'association " . $value->getName() . " : " . $tab_doublon[$value->getName()];
            if (in_array($value, $new_tab) && $tab_doublon[$value->getName()] == $number) {
                return True;
            } else {
                return False;
            }
        }

        $allParticipants = $this->userRepository->findAll();
        // Récupération des participants => Trouver un moyen de les ranger par taille d'association
        $participants = $this->userRepository->findByAssociationNotNull();
        $tableRondes = $this->tableRondeRepository->findAll();

        foreach ($this->associationRepository->findAll() as $item) {
            $item->setTotalMembers();
            $manager->flush();
        }

        // On enlève l'affectation aux tables rondes
        foreach ($allParticipants as $participant) {
            foreach ($participant->getTableRondes() as $tableRonde) {
                $participant->removeTableRonde($tableRonde);
            }
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

        // On crée les tables rondes pour le 1er tour
        for ($i = 0; $i < $nbTables; $i++) {
            $tableRonde = new TableRonde();
            $tableRonde->setTableNumber($i + 1);
            $tableRonde->setRound($roundActuel);

            $manager->persist($tableRonde);
        }

        $manager->flush();

        // Création des tableaux des tables rondes
        $i = 1;
        foreach ($tableRondes as $tableRonde) {
            ${'table' . $i} = array();
            ${'tableAssociation' . $i} = array();
            $i++;
        }

        $associations = $this->associationRepository->findByMembers();

        // On boucle sur les associations
        foreach ($associations as $association) {
            var_dump('Asso : ' . $association->getName());

            // On boucle sur les participants
            foreach ($association->getUsers() as $participant) {
                var_dump('Utilisateur ' . $participant->getId());

                foreach ($tableRondes as $tableRonde) {
                    var_dump('Table ' . $tableRonde->getTableNumber());
                    $numeroTable = $tableRonde->getTableNumber(); // On récupère le numéro de la table
                    $table = $this->tableRondeRepository->findByTableNumber($numeroTable); // On récupère la table
                    if (count(${'table' . $numeroTable}) == $nbMaxPerTable) {
                        var_dump('La table ' . $numeroTable . ' est pleine -> ' . count(${'table' . $numeroTable}));
                        continue;
                    }
                    if (!in_array($participant, ${'table' . $numeroTable}) && count(${'table' . $numeroTable}) < $nbMaxPerTable && !in_array($participant->getAssociation(), ${'tableAssociation' . $numeroTable}) && count($participant->getTableRondes()) === 0) {

                        $participant->addTableRonde($table);
                        ${'table' . $numeroTable}[] = $participant;
                        ${'tableAssociation' . $numeroTable}[] = $participant->getAssociation();

                        $manager->persist($participant);

                        var_dump('Utilisateur ' . $participant->getId() . ' ajouté à la table ' . $tableRonde->getTableNumber());
                        break;
                    }

                }
            }
        }
        
        $manager->flush();

        echo count($tableAssociation1);
        
        // On boucle sur les associations
        foreach ($associations as $association) {
            var_dump('Asso : ' . $association->getName());

            // On boucle sur les participants restants
            $resteParticipants = array(); // Tableau des participants restants
            foreach ($participants as $participant) {
                if (count($participant->getTableRondes()) == 0) {
                    $resteParticipants[] = $participant;
                }
            }
            
            foreach ($resteParticipants as $participant) {
                var_dump('Utilisateur ' . $participant->getId());

                foreach ($tableRondes as $tableRonde) {
                    var_dump('Table ' . $tableRonde->getTableNumber());
                    $numeroTable = $tableRonde->getTableNumber(); // On récupère le numéro de la table
                    $table = $this->tableRondeRepository->findByTableNumber($numeroTable); // On récupère la table
                    
                    // On récupère le pourcentage 
                    if (count(${'table' . $numeroTable}) == $nbMaxPerTable) {
                        var_dump('La table ' . $numeroTable . ' est pleine -> ' . count(${'table' . $numeroTable}));
                        continue;
                    }
                    if (!in_array($participant, ${'table' . $numeroTable}) && count(${'table' . $numeroTable}) < $nbMaxPerTable && in_array_once($participant->getAssociation(), ${'tableAssociation' . $numeroTable}, 1) && count($participant->getTableRondes()) === 0) {

                        $participant->addTableRonde($table);
                        ${'table' . $numeroTable}[] = $participant;

                        // On pourra voir ici pour mettre un nombre maximum de membres de cette association sur une table (en fonction du pourcentage du total des membres)
                        // ${'tableAssociation' . $numeroTable}[] = $participant->getAssociation();

                        $manager->persist($participant);

                        var_dump('Utilisateur ajouté à la table ' . $tableRonde->getTableNumber());
                    }

                }
            }
        }
        
        $manager->flush();
    }


    public static function getGroups(): array
    {
        return ['group2'];
    }
}
