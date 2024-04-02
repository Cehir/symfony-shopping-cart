<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{

    /**
     * @inheritDoc
     */
    #[\Override] public function load(ObjectManager $manager): void
    {
        ProductFactory::createMany(10);
    }
}
