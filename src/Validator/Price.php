<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
class Price extends Constraint
{
    public function __construct(
        public ?string $mode = 'strict',
        public ?string $message = 'The string "{{ string }}" should be in the format of "0.00 EUR"',
        array|null $groups = null,
        mixed $payload = null)
    {
        parent::__construct(groups: $groups, payload: $payload);
    }

}
