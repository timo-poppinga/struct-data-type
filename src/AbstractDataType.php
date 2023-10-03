<?php

declare(strict_types=1);

namespace Struct\DataType;

use Struct\Contracts\DataType\DataTypeInterface;

abstract class AbstractDataType implements DataTypeInterface
{
    public function __construct(?string $serializedData = null)
    {
        if ($serializedData === null) {
            return;
        }
        $this->_deserializeFromString($serializedData);
    }

    protected function _deserializeFromString(string $serializedData): void
    {
        throw new \RuntimeException('Must be implemented', 1696233161);
    }

    protected function _serializeToString(): string
    {
        throw new \RuntimeException('Must be implemented', 1696233161);
    }

    public function serializeToString(): string
    {
        return $this->_serializeToString();
    }

    public static function deserializeFromString(string $serializedData): static
    {
        $class = static::class;
        $dataType = new $class();
        $dataType->_deserializeFromString($serializedData);
        return $dataType;
    }

    public function __toString(): string
    {
        return $this->serializeToString();
    }
}
