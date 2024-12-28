<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class ARule extends AbstractRule
{
    public function __construct(public Argument|Promise $root, public Argument|Promise $class, public bool $allowString)
    {
        parent::__construct(new MultiRule(
            false,
            new TypeRule($class, 'string'),
            $allowString ? new MultiRule(
                true,
                new TypeRule($root, 'string'),
                new TypeRule($root, 'object'),
            ) : new TypeRule($root, 'string'),
            new CallbackRule(function (Data $data, bool $not, ?string $message) {
                $root = $data->getArgument($this->root);
                $class = $data->getArgument($this->class);

                return is_a($root->value, $class->value, $this->allowString) === $not
                    ? $message ?? ($root . ($not ? ' not' : '') . ' must be of type ' . $class)
                    : null;
            }),
        ));
    }
}