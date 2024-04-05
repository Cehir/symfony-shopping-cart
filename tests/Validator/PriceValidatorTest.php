<?php

namespace App\Tests\Validator;

use App\Validator\Price;
use App\Validator\PriceValidator;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<PriceValidator>
 */
class PriceValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new PriceValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Price());

        $this->assertNoViolation();
    }

    public function testValidPrice(): void
    {
        $this->validator->validate('22.22 EUR', new Price());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider provideInvalidConstraints
     */
    public function testInvalidPrices(string $value, Price|Currency $constraint): void
    {
        $this->validator->validate($value, $constraint);

        $this->buildViolation('some error')
            ->setParameter('{{ string }}', $value)
            ->assertRaised();
    }

    public function provideInvalidConstraints(): \Generator
    {
        yield ['some text' ,new Price(message: 'some error')];
        yield ['.22 EUR' ,new Price(message: 'some error')]; //missing leading number
        yield ['2.22' ,new Price(message: 'some error')]; //missing currency
        yield ['2.0 EUR', new Price(message: 'some error')]; //missing fraction
    }

}
