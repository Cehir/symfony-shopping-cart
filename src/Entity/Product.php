<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Groups(["product:item"])]
class Product extends AbstractEntity
{
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
}
