<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class NumericRule extends AbstractRule
{
    public function __construct(Argument|Promise $root)
    {
        parent::__construct(new CallbackRule(
            static fn (Data $data, bool $not, ?string $message) => is_numeric($root->value) === $not
                ? $message ?? ($root . ($not ? ' not' : '') . ' must be numeric')
                : null,
        ));
    }
}