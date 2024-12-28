<?php

declare(strict_types = 1);

namespace EugeneErg\Assert;

use RuntimeException;
use Throwable;

final class ValidationException extends RuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}