<?php

declare(strict_types = 1);

namespace EugeneErg\Assert;

use Stringable;

final readonly class Argument implements Stringable
{
    public function __construct(
        public mixed $value,
        public ?string $name = null,
    ) {
    }

    public static function fromValue(mixed $value, ?string $name = null): self
    {
        return $value instanceof self ? $value : new self($value, $name);
    }

    public function __toString(): string
    {
        return $this->name ?? match (gettype($this->value)) {
            'integer', 'double' => (string) $this->value,
            'NULL' => 'null',
            'string' => '"' . str_replace('"', '""', $this->value) . '"',
            'boolean' => $this->value ? 'true' : 'false',
            'object' => $this->value::class . '(...)',
            default => gettype($this->value) . '(...)',
        };
    }
}