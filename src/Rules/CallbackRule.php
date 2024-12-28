<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Data;

final readonly class CallbackRule extends AbstractRule
{
    /** @var callable */
    public mixed $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
        parent::__construct();
    }

    public function validate(Data $data, bool $not, ?string $message = null): ?string
    {
        return ($this->callback)($data, $not, $message);
    }
}