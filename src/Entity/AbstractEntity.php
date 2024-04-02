<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Uid\Uuid;

/**
 * AbstractEntity class represents an entity with common properties and methods.
 */
#[Groups(["product:item", "shop:item"])]
abstract class AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(["product:list", "shop:list"])]
    protected Uuid $id;

    #[ORM\Column(type: "datetime_immutable")]
    #[ORM\OrderBy(["createdAt" => "DESC"])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::RFC3339])]
    #[SerializedName("created_at")]
    #[Groups(["shop:list"])]
    protected DateTimeImmutable $createdAt;

    public function __construct(
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
