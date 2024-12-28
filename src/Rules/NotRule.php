<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Data;

final readonly class NotRule extends AbstractRule
{
    public function __construct(public AbstractRule $rule)
    {
        parent::__construct(new CallbackRule(static fn (Data $data, bool $not, ?string $message) => $rule->validate($data, !$not, $message)));
    }
}