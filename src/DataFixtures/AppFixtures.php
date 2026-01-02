<?php

namespace App\DataFixtures;

use App\Factory\ProspectFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $prospect = ProspectFactory::createMany(2);

        $manager->flush();
    }
}
