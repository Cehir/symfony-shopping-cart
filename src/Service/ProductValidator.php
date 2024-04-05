<?php

namespace App\Service;

use App\Validator\Price;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductValidator
{
    public function __construct(protected ValidatorInterface $validator)
    {
    }

    /**
     * @param array<mixed> $productUpdateData
     * @return ConstraintViolationListInterface
     */
    public function validateUpdate(mixed $productUpdateData): ConstraintViolationListInterface
    {
        $constraint = new Assert\Collection([
            'amount' => new Assert\Optional([
                new Assert\Type('int'),
                new Assert\Range(['min' => 1, 'max' => 10])
            ]),
            'product' => new Assert\Optional(
                new Assert\Collection([
                    'name' => new Assert\Optional([
                        new Assert\Type('string'),
                        new Assert\Length(min: 3, max: 50)
                    ]),
                    'price' => new Assert\Optional([
                        new Assert\NotNull(),
                        new Assert\Type('string'),
                        new Price(),
                    ])
                ])
            )
        ]);

        return $this->validator->validate(value: $productUpdateData, constraints: $constraint);
    }
}
