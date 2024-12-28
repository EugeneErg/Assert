<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Data;

abstract readonly class AbstractRule
{
    public function __construct(
        public ?self $parent = null,
    ) {
    }

    public function validate(Data $data, bool $not, ?string $message = null): ?string
    {
        return $this->parent?->validate($data, $not, $message ?? $this->getMessage($data, $not));
    }

    public function getMessage(Data $data, bool $not): ?string
    {
        return null;
    }
}
