<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Association;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * L'encodeur de mot de passe
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de l'utilisateur administrateur
        $user = new User;

        $hash = $this->encoder->encodePassword($user, "Bluecom86");

        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($hash);
        $user->setEmail('dimbo@blue-com.fr');
        $user->setFirstname($faker->firstName);
        $user->setLastname($faker->lastName);
        $user->setPhone($faker->e164PhoneNumber);

        $manager->persist($user);
        $manager->flush();
        
        // Création des associations
        for ($i = 0; $i < 10; $i++) {
            $association = new Association();
            $association->setName($faker->company);
            $association->setDescription($faker->text);

            $manager->persist($association);

            // Création des utilisateurs
            for ($j = 0; $j < mt_rand(20, 100); $j++) {
                $user = new User();
                $user->setEmail($faker->email);
                $user->setFirstname($faker->firstName);
                $user->setLastname($faker->lastName);
                $user->setPhone($faker->e164PhoneNumber);
                $user->setRoles(['ROLE_USER']);
                $user->setPassword($this->encoder->encodePassword($user, 'password'));
                $user->setAssociation($association);

                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}
