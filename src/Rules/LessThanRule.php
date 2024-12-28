<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class LessThanRule extends AbstractRule
{
    public function __construct(
        public Argument|Promise $root,
        public Argument|Promise $value,
        public bool $equal = true,
    ) {
        parent::__construct(new CallbackRule(static fn (Data $data, bool $not, ?string $message) => $this->lessThan(
            $not,
            $data->getArgument($root),
            $data->getArgument($value),
            $equal,
            $message,
        )));
    }

    private static function lessThan(bool $not, Argument $root, Argument $value, bool $equal, ?string $message): ?string
    {
        if ($not) {
            if (
                ($equal && $value->value >= $root->value)
                || (!$equal && $value->value > $root)
            ) {
                return $message ?? ($root
                    . ' must be greater than ' . ($equal ? '' : 'or equal ')
                    . $value);
            }

            return null;
        }

        if (
            ($equal && $value->value < $root->value)
            || (!$equal && $value <= $root->value)
        ) {
            return $message ?? ($root
                . ' must be less than ' . ($equal ? 'or equal ' : '')
                . $value);
        }

        return null;
    }
}