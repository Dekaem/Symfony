<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Association;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{

    private $hasher;

    public function __construct(
        UserPasswordHasherInterface $hasher
    ) {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de l'utilisateur administrateur
        $user = new User;

        $hash = $this->hasher->hashPassword($user, "admini");

        $user->setRoles(["ROLE_ADMIN"]);
        $user->setEmail('admin@blue-com.fr');
        $user->setPassword($hash);
        $user->setFirstname('Admin');
        $user->setLastname('');
        $user->setPhone('');

        $manager->persist($user);
        $manager->flush();
        
        // Création des associations
        for ($i = 0; $i < 8; $i++) {
            $association = new Association();
            $association->setName($faker->company);
            $association->setDescription($faker->text);
            $association->setTotalMembers();

            $manager->persist($association);

            // Création des utilisateurs
            for ($j = 0; $j < mt_rand(20, 100); $j++) {
                $user = new User();
                $user->setEmail($faker->email);
                $user->setFirstname($faker->firstName);
                $user->setLastname($faker->lastName);
                $user->setPhone($faker->e164PhoneNumber);
                $user->setRoles(['ROLE_USER']);
                $user->setPassword($this->hasher->hashPassword($user,'password'));
                $user->setAssociation($association);

                $manager->persist($user);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
