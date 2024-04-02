<?php

namespace App\Repository;

use App\Entity\ShoppingCart;
use App\Entity\ShoppingCartProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @template T of ShoppingCart
 * @extends ServiceEntityRepository<ShoppingCart>
 */
class ShoppingCartRepository extends ServiceEntityRepository
{

    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingCart::class);
    }

    public function createShoppingCart(): ShoppingCart
    {
        $shoppingCart = new ShoppingCart();

        $entityManager = $this->registry->getManager();
        $entityManager->persist($shoppingCart);
        $entityManager->flush();

        return $shoppingCart;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function removeByID(string $id): void
    {
        $entityManager = $this->getEntityManager();
        $entity = $entityManager->find(ShoppingCart::class, $id);

        if ($entity === null) {
            throw new NotFoundHttpException();
        }

        $entityManager->remove($entity);
        $entityManager->flush();
    }
}
