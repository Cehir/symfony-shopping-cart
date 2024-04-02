<?php

namespace App\Factory;

use App\Entity\ShoppingCartProduct;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ShoppingCartProduct>
 *
 * @method        ShoppingCartProduct|Proxy        create(array|callable $attributes = [])
 * @method static ShoppingCartProduct|Proxy        createOne(array $attributes = [])
 * @method static ShoppingCartProduct|Proxy        find(object|array|mixed $criteria)
 * @method static ShoppingCartProduct|Proxy        findOrCreate(array $attributes)
 * @method static ShoppingCartProduct|Proxy        first(string $sortedField = 'id')
 * @method static ShoppingCartProduct|Proxy        last(string $sortedField = 'id')
 * @method static ShoppingCartProduct|Proxy        random(array $attributes = [])
 * @method static ShoppingCartProduct|Proxy        randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static ShoppingCartProduct[]|Proxy[]    all()
 * @method static ShoppingCartProduct[]|Proxy[]    createMany(int $number, array|callable $attributes = [])
 * @method static ShoppingCartProduct[]|Proxy[]    createSequence(iterable|callable $sequence)
 * @method static ShoppingCartProduct[]|Proxy[]    findBy(array $attributes)
 * @method static ShoppingCartProduct[]|Proxy[]    randomRange(int $min, int $max, array $attributes = [])
 * @method static ShoppingCartProduct[]|Proxy[]    randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<ShoppingCartProduct> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<ShoppingCartProduct> createOne(array $attributes = [])
 * @phpstan-method static Proxy<ShoppingCartProduct> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<ShoppingCartProduct> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<ShoppingCartProduct> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<ShoppingCartProduct> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<ShoppingCartProduct> random(array $attributes = [])
 * @phpstan-method static Proxy<ShoppingCartProduct> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<ShoppingCartProduct> repository()
 * @phpstan-method static list<Proxy<ShoppingCartProduct>> all()
 * @phpstan-method static list<Proxy<ShoppingCartProduct>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<ShoppingCartProduct>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<ShoppingCartProduct>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<ShoppingCartProduct>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<ShoppingCartProduct>> randomSet(int $number, array $attributes = [])
 */
final class ShoppingCartProductFactory extends ModelFactory
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
            'amount' => self::faker()->randomNumber(1),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(ShoppingCartProduct $shoppingCartProduct): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ShoppingCartProduct::class;
    }
}
