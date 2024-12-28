<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use Countable;
use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class CountLessThanRule extends AbstractRule
{
    public function __construct(
        public Argument|Promise $root,
        public Argument|Promise $value,
        public bool $equal = true,
    ) {
        parent::__construct(new MultiRule(
            false,
            new MultiRule(
                true,
                new TypeRule($root, 'array'),
                new TypeRule($root, Countable::class),
            ),
            new CallbackRule(static function (Data $data, bool $not, ?string $message) use ($root, $value, $equal) {
                $root = $data->getArgument($root);

                return (new LessThanRule(
                    Argument::fromValue(count($root->value), $root->name),
                    $value,
                    $equal,
                ))->validate($data, $not, $message);
            }),
        ));
    }

    public function getMessage(Data $data, bool $not): ?string
    {
        $root = $data->getArgument($this->root);
        $value = $data->getArgument($this->value);

        return 'Count of ' . $not
            ? $root
                . ' must be greater than ' . ($this->equal ? '' : 'or equal ')
                . $value
            : $root
                . ' must be less than ' . ($this->equal ? 'or equal ' : '')
                . $value;
    }
}