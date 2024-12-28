<?php

declare(strict_types = 1);

namespace EugeneErg\Assert;

use Generator;

final readonly class Data
{
    private function __construct(public mixed $data, public ?self $parent = null, public array $path = [])
    {
    }

    public static function create(mixed $data): self
    {
        return new self($data);
    }

    public function getArgument(mixed $value): Argument
    {
        if ($value instanceof Argument) {
            return $value;
        }

        if (!$value instanceof Promise) {
            return Argument::fromValue($value);
        }

        $data = $this;

        for ($level = 0; $level < $value->level; $level++) {
            $data = $data->parent;
        }

        $result = array_reduce($value->path, static fn(mixed $datum, string $step) => $datum[$step], $data->data);

        $paths = array_merge($data->path, $value->path);

        return Argument::fromValue($result, $paths === [] ? null : implode('.', $paths));
    }

    public function getByPromise(Promise $promise): self
    {
        $data = $this;

        for ($level = 0; $level < $promise->level; $level++) {
            $data = $data->parent;
        }

        foreach ($promise->path as $key) {
            $data = $data->getChild($key);
        }

        return $data;
    }

    public function getChild(string $key): self
    {
        $path = $this->path;
        $path[] = $key;

        return new self($this->data[$key], $this, $path);
    }

    /**
     * @return Generator<self>
     */
    public function getChildren(): Generator
    {
        foreach ($this->data as $key => $value) {
            yield $key => $this->getChild($key);
        }
    }
}