<?php

namespace App\Entity;

use App\Repository\ShoppingCartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: ShoppingCartRepository::class)]
class ShoppingCart extends AbstractEntity
{
    /**
     * @var ShoppingCartProduct[]
     */
    #[ORM\OneToMany(targetEntity: ShoppingCartProduct::class, mappedBy: 'shoppingCart', cascade: ['persist', 'remove'])]
    #[ORM\Embedded]
    #[MaxDepth(3)]
    #[SerializedName("products")]
    #[Groups(["shop:item"])]
    protected iterable $shoppingCartItems;

    public function __construct()
    {
        parent::__construct();

        $this->shoppingCartItems = new ArrayCollection();
    }

    /**
     * @return iterable<ShoppingCartProduct>
     */
    public function getShoppingCartItems(): iterable
    {
        return $this->shoppingCartItems;
    }

    /**
     * Adds a product to the list of products.
     *
     * @param Product $productToAdd The product to be added.
     * @return void
     */
    public function addOneProduct(Product $productToAdd): void
    {
        /** @var ArrayCollection<int, ShoppingCartProduct> $products */
        $products = $this->shoppingCartItems;

        //simply append the product on an empty list
        if ($products->count() == 0) {
            $products->add(new ShoppingCartProduct($this, $productToAdd, 1));
            return;
        }

        //matching product id
        foreach ($products as $product) {
            if ($product->getProduct()->getId() === $productToAdd->getId()) {
                $product->increaseAmountBy(1);
                return;
            }
        }

        // product not found, add a new ShoppingCartProducts instance
        $products->add(new ShoppingCartProduct($this, $productToAdd, 1));
    }

    /**
     * Removes one quantity of a product from the shopping cart.
     *
     * @param Product $product The product to remove.
     * @return ShoppingCartProduct|null The removed ShoppingCartProducts instance, or null if the product was not found or the amount was already 0.
     */
    public function removeOneProduct(Product $product): ?ShoppingCartProduct
    {
        /** @var ArrayCollection<int, ShoppingCartProduct> $products */
        $products = $this->shoppingCartItems;

        // if there are no products, then there is nothing to remove
        if ($products->count() == 0) {
            return null;
        }

        // find the ShoppingCartProducts instance with the matching product id
        foreach ($products as $key => $shoppingCartProduct) {
            if ($shoppingCartProduct->getProduct()->getId() === $product->getId()) {
                $shoppingCartProduct->decreaseAmountBy(1);

                if ($shoppingCartProduct->getAmount() <= 0) {
                    $products->remove($key);
                    return $shoppingCartProduct;
                }
            }
        }

        return null;
    }

    /**
     * @param int $amount
     * @param Product $product
     * @return ShoppingCartProduct|null if a ShoppingCartProduct is returned it should be deleted
     */
    public function setAmountOnProduct(int $amount, Product $product): ?ShoppingCartProduct
    {
        /** @var ArrayCollection<int, ShoppingCartProduct> $products */
        $products = $this->shoppingCartItems;

        // if there are no products, then there is nothing to update
        if ($products->count() == 0) {
            return null;
        }

        // find the ShoppingCartProducts instance with the matching product id
        foreach ($products as $key => $shoppingCartProduct) {
            if ($shoppingCartProduct->getProduct()->getId() === $product->getId()) {
                $shoppingCartProduct->setAmount($amount);

                if ($shoppingCartProduct->getAmount() <= 0) {
                    $products->remove($key);
                    return $shoppingCartProduct;
                }
            }
        }

        return null;
    }
}
