<?php

namespace Rpungello\WeightedTable;

use ArrayAccess;
use Countable;

class Row implements Countable, ArrayAccess
{
    public function __construct(protected array $cells = [], protected ?string $class = null)
    {
    }

    /**
     * @return array
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    public function hasClass(): bool
    {
        return ! is_null($this->class);
    }

    public function count(): int
    {
        return count($this->cells);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->cells);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->cells[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->cells[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->cells[$offset]);
    }
}
