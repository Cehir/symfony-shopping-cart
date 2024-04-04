<?php

namespace App\Tests\Service;

use App\Service\ProductValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ProductValidatorTest extends TestCase
{
    private ProductValidator|null $shoppingCartService = null;

    public function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->getValidator();
        $this->shoppingCartService = new ProductValidator($validator);
    }

    public function testValidateUpdateWithoutData(): void
    {
        $service = $this->shoppingCartService;

        $productUpdateData = [];
        $violations = $service?->validateUpdate($productUpdateData);

        $this->assertNotNull($violations);
        $this->assertCount(0, $violations);
    }

    public function testValidateUpdateAddingToFewProducts(): void
    {
        $service = $this->shoppingCartService;

        $productUpdateData = ['amount' => 0];
        $violations = $service?->validateUpdate($productUpdateData);

        $this->assertNotNull($violations);
        $this->assertCount(1, $violations);
    }

    public function testValidateUpdateAddingToManyProducts(): void
    {
        $service = $this->shoppingCartService;

        $productUpdateData = ['amount' => 11];
        $violations = $service?->validateUpdate($productUpdateData);

        $this->assertNotNull($violations);
        $this->assertCount(1, $violations);
    }

    public function testValidateUpdateNameWithANumber(): void {
        $service = $this->shoppingCartService;

        $productUpdateData = ['product' => ['name' => 1000]];
        $violations = $service?->validateUpdate($productUpdateData);

        $this->assertNotNull($violations);
        $this->assertCount(1, $violations);
    }

    public function testValidateUpdateNameWithAnEmptyString(): void {
        $service = $this->shoppingCartService;

        $productUpdateData = ['product' => ['name' => ""]];
        $violations = $service?->validateUpdate($productUpdateData);

        $this->assertNotNull($violations);
        $this->assertCount(1, $violations);
    }

    public function testValidateUpdateName(): void {
        $service = $this->shoppingCartService;

        $productUpdateData = ['product' => ['name' => "This Should Do It"]];
        $violations = $service?->validateUpdate($productUpdateData);

        $this->assertNotNull($violations);
        $this->assertCount(0, $violations);
    }
}
