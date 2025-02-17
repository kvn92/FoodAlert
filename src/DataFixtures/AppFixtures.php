<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Générer 10 utilisateurs aléatoires
        UserFactory::createMany(10);

        // Appliquer les changements en base
        $manager->flush();
    }
}

