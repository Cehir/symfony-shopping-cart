<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Product>
 */
final class ProductFactory extends PersistentObjectFactory
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
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->firstName(),
            'price' => sprintf('%.2f EUR', self::faker()->randomFloat(2, 10, 1000))
        ];
    }

    public static function class(): string
    {
        return Product::class;
    }
}
