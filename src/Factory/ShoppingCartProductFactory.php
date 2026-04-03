<?php

namespace App\Factory;

use App\Entity\ShoppingCartProduct;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ShoppingCartProduct>
 */
final class ShoppingCartProductFactory extends PersistentObjectFactory
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
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    private function getDefaults(): array
    {
        return [
            'amount' => self::faker()->randomNumber(1),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ShoppingCartProduct $shoppingCartProduct): void {})
        ;
    }

    private static function getClass(): string
    {
        return ShoppingCartProduct::class;
    }

    protected function defaults(): array|callable
    {
        return $this->getDefaults();
    }

    public static function class(): string
    {
        return self::getClass();
    }
}
