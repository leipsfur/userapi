<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setId("1");
        $user->setLogin("henry.dunant");
        $user->setFirstName("Henry");
        $user->setLastName("Dunant");
        $manager->persist($user);
        $user = new User();
        $user->setId("2");
        $user->setLogin("bertha.suttner");
        $user->setFirstName("Bertha");
        $user->setLastName("Suttner");
        $manager->persist($user);
        $user = new User();
        $user->setId("3");
        $user->setLogin("jane.doe");
        $user->setFirstName("Jane");
        $user->setLastName("Doe");
        $manager->persist($user);
        $user = new User();
        $user->setId("4");
        $user->setLogin("john.doe");
        $user->setFirstName("John");
        $user->setLastName("Doe");
        $manager->persist($user);

        $manager->flush();
    }
}