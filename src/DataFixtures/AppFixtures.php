<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->userPasswordHasher= $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $userAdmin = new User();
        $userAdmin->setEmail("admin@montictac.fr");
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, 'password'));
        $manager->persist($userAdmin);
        $manager->flush();

        $user = new User();
        $user->setEmail("sylvain@montictac.fr");
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $manager->flush();

        $activity= new Activity();
        $activity->setTitle('Coding monTicTac' );
        $activity->setDescription('Ma première API Symfony');
        $activity->setUser($userAdmin);
        $manager->persist($activity);
        

        $activity= new Activity();
        $activity->setTitle('Marche' );
        $activity->setDescription('Objectif au moins une heure par jour');
        $activity->setUser($user);
        $manager->persist($activity);

        $manager->flush();
    }
}
