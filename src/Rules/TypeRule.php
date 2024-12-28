<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class TypeRule extends AbstractRule
{
    public function __construct(public Argument|Promise $root, public string $type)
    {
        parent::__construct(new CallbackRule(function (Data $data, bool $not, ?string $message) use ($root) {
            $root = $data->getArgument($root);
            $success = class_exists($this->type)
                ? $root->value instanceof $this->type
                : gettype($root->value) === $this->type;

            return $success === $not ? $message ?? $this->createMessage($root, $not) : null;
        }));
    }

    private function createMessage(Argument $root, bool $not): string
    {
        $type = $this->type;
        $given = class_exists($this->type) && is_object($root->value)
            ? $root::class
            : gettype($root->value);

        return $root . ($not ? ' not' : '') . ' must be of type ' . $type . ', ' . $given . ' given';
    }
}