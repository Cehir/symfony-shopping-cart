<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ShoppingCart;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ShoppingCart>
 */
final class ShoppingCartFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(ShoppingCart $shoppingCart): void {})
            ;
    }

    protected function defaults(): array|callable
    {
        return [];
    }

    public static function class(): string
    {
        return ShoppingCart::class;
    }
}
