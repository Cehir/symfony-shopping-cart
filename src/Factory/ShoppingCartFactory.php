<?php

namespace App\Factory;

use App\Entity\ShoppingCart;
use App\Repository\ShoppingCartRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ShoppingCart>
 *
 * @method        ShoppingCart|Proxy                     create(array|callable $attributes = [])
 * @method static ShoppingCart|Proxy                     createOne(array $attributes = [])
 * @method static ShoppingCart|Proxy                     find(object|array|mixed $criteria)
 * @method static ShoppingCart|Proxy                     findOrCreate(array $attributes)
 * @method static ShoppingCart|Proxy                     first(string $sortedField = 'id')
 * @method static ShoppingCart|Proxy                     last(string $sortedField = 'id')
 * @method static ShoppingCart|Proxy                     random(array $attributes = [])
 * @method static ShoppingCart|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ShoppingCartRepository|RepositoryProxy repository()
 * @method static ShoppingCart[]|Proxy[]                 all()
 * @method static ShoppingCart[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ShoppingCart[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ShoppingCart[]|Proxy[]                 findBy(array $attributes)
 * @method static ShoppingCart[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ShoppingCart[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<ShoppingCart> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<ShoppingCart> createOne(array $attributes = [])
 * @phpstan-method static Proxy<ShoppingCart> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<ShoppingCart> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<ShoppingCart> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<ShoppingCart> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<ShoppingCart> random(array $attributes = [])
 * @phpstan-method static Proxy<ShoppingCart> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<ShoppingCart> repository()
 * @phpstan-method static list<Proxy<ShoppingCart>> all()
 * @phpstan-method static list<Proxy<ShoppingCart>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<ShoppingCart>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<ShoppingCart>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<ShoppingCart>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<ShoppingCart>> randomSet(int $number, array $attributes = [])
 */
final class ShoppingCartFactory extends ModelFactory
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
    protected function getDefaults(): array
    {
        return [
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this// ->afterInstantiate(function(ShoppingCart $shoppingCart): void {})
            ;
    }

    protected static function getClass(): string
    {
        return ShoppingCart::class;
    }
}
