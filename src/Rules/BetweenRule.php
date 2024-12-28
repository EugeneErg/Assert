<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class BetweenRule extends AbstractRule
{
    public function __construct(
        public Argument|Promise $root,
        public Argument|Promise $from,
        public Argument|Promise $to,
        public bool $fromEqual = true,
        public bool $toEqual = true,
    ) {
        parent::__construct(new MultiRule(
            false,
            new LessThanRule($this->from, $this->root, $this->fromEqual),
            new LessThanRule($this->root, $this->to, $this->toEqual),
        ));
    }

    public function getMessage(Data $data, bool $not): ?string
    {
        $root = $data->getArgument($this->root);
        $from = $data->getArgument($this->from);
        $to = $data->getArgument($this->to);

        return $not
            ? $root
                . ' or greater than ' . ($this->fromEqual ? ' or equal' : '')
                . $from
                . ' must be less than ' . ($this->toEqual ? ' or equal' : '')
                . $to
            : $root
                . ' must be less than ' . ($this->fromEqual ? '' : ' or equal')
                . $from
                . ' or greater than ' . ($this->toEqual ? '' : ' or equal')
                . $to;
    }
}