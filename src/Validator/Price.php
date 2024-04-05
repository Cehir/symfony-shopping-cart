<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Price extends Constraint
{
    public string $message = 'The string "{{ string }}" should be in the format of "0.00 EUR"';
    public string $mode = 'strict';

    public function __construct(string $mode = null, string $message = null, array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->mode = $mode ?? $this->mode;
        $this->message = $message ?? $this->message;
    }

}
