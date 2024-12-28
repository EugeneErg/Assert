<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class CountGreaterThanRule extends AbstractRule
{
    public function __construct(
        public Argument|Promise $root,
        public Argument|Promise $value,
        public bool $equal = true,
    ) {
        parent::__construct(new CountLessThanRule($this->value, $this->root, $this->equal));
    }

    public function getMessage(Data $data, bool $not): ?string
    {
        $root = $data->getArgument($this->root);
        $value = $data->getArgument($this->value);

        return 'Count of ' . $not
            ? $root
                . ' must be less than ' . ($this->equal ? '' : 'or equal ')
                . $value
            : $root
                . ' must be greater than ' . ($this->equal ? 'or equal ' : '')
                . $value;
    }
}