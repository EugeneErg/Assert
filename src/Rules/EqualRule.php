<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class EqualRule extends AbstractRule
{
    public function __construct(
        public Argument|Promise $valueA,
        public Argument|Promise $valueB,
        public bool $strict,
    ) {
        parent::__construct(new CallbackRule(function (Data $data, bool $not, ?string $message) {
            $valueA = $data->getArgument($this->valueA);
            $valueB = $data->getArgument($this->valueB);

            return (($this->strict && $valueA->value === $valueB->value)
                || (!$this->strict && $valueA->value == $valueB->value)) === $not
                ? $message ?? ($valueA . ($not ? ' not' : '') . ' must be equal ' . $valueB)
                : null;
        }));
    }
}