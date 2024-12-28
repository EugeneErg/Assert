<?php

declare(strict_types = 1);

namespace EugeneErg\Assert;

final readonly class Promise
{
    public array $path;

    public function __construct(public int $level = 0, string ...$path)
    {
        $this->path = $path;
    }
}