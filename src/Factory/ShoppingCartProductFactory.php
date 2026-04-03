<?php

declare(strict_types=1);

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
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ShoppingCartProduct $shoppingCartProduct): void {})
        ;
    }

    protected function defaults(): array|callable
    {
        return [
            'amount' => self::faker()->randomNumber(1),
        ];
    }

    public static function class(): string
    {
        return ShoppingCartProduct::class;
    }
}
