<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class CallableRule extends AbstractRule
{
    public function __construct(public Argument|Promise $root)
    {
        parent::__construct(new CallbackRule(
            function (Data $data, bool $not, ?string $message) {
                $root = $data->getArgument($this->root);

                return is_callable($root->value) === $not
                    ? $message ?? ($root . ($not ? ' not' : '') . ' must be callable')
                    : null;
            },
        ));
    }
}