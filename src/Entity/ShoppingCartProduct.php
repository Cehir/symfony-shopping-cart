<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class ShoppingCartProduct
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    protected Uuid $id;

    #[ORM\Column(type: "datetime_immutable")]
    #[ORM\OrderBy(["createdAt" => "DESC"])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::RFC3339])]
    #[SerializedName("created_at")]
    protected DateTimeImmutable $createdAt;
    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'shopping_cart_products')]
        protected ShoppingCart      $shoppingCart,

        #[ORM\ManyToOne(inversedBy: 'shopping_cart_products')]
        #[Groups(["shop:item"])]
        protected Product           $product,

        #[ORM\Column]
        #[Groups(["shop:item"])]
        protected int $amount
    )
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param int $amount
     * @return void
     */
    public function increaseAmountBy(int $amount): void
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must be a positive integer.');
        }
        $this->amount += $amount;
    }

    public function decreaseAmountBy(int $amount): void
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must be a positive integer.');
        }
        $this->amount -= $amount;

        if ($this->amount < 0) {
            $this->amount = 0;
        }
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
