<?php

namespace App\DataFixtures;

use App\Entity\ShoppingCart;
use App\Factory\ShoppingCartFactory;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ShoppingCartFixtures extends Fixture
{

    /**
     * @inheritDoc
     * @throws \Exception
     */
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        ShoppingCartFactory::createMany(3);
    }
}
