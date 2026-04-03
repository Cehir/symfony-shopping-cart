<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\ShoppingCartFactory;
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
