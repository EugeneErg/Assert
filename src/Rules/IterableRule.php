<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;
use EugeneErg\Assert\PromiseValidator;

final readonly class IterableRule extends AbstractRule
{
    public function __construct(public Argument|Promise $root)
    {
        parent::__construct(PromiseValidator::callback(function (Data $data, bool $not, ?string $message) {
            $root = $data->getArgument($this->root);

            return is_iterable($root->value) === $not
                ? $message ?? ($root . ($not ? ' not' : '') . ' must be of type Traversable|array, ' . gettype($root->value) . ' given')
                : null;
        })->rule);
    }
}