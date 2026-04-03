<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Groups(["product:item"])]
class Product extends AbstractEntity
{
    /**
     * @var Collection<int, ShoppingCartProduct>
     */
    #[ORM\OneToMany(targetEntity: ShoppingCartProduct::class, mappedBy: 'product', cascade: ['refresh', 'persist', 'remove', 'detach'], orphanRemoval: true)]
    private Collection $shoppingCartProducts;

    public function __construct(
        #[ORM\Column(type: "string", length: 255)]
        #[Assert\NotBlank(message: "Please enter a product name.")]
        #[Groups(["product:list", 'shop:item'])]
        private string  $name,

        #[ORM\Column(length: 32, nullable: true)]
        #[Groups(["product:list", "shop:item"])]
        private ?string $price = null
    )
    {
        parent::__construct();
        $this->shoppingCartProducts = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, ShoppingCartProduct>
     */
    public function getShoppingCartProducts(): Collection
    {
        return $this->shoppingCartProducts;
    }
}
