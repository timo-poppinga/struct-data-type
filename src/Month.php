<?php

declare(strict_types=1);

namespace Struct\DataType;

use InvalidArgumentException;
use Struct\Exception\Serialize\DeserializeException;

final class Month extends AbstractDataType
{
    protected int $year;

    protected int $month;

    public function setMonth(int $month): void
    {
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('The month must be between 1 and 12', 1696052867);
        }
        $this->month = $month;
    }

    public function setYear(int $year): void
    {
        if ($year < 1000 || $year > 9999) {
            throw new InvalidArgumentException('The year must be between 1000 and 9999', 1696052931);
        }
        $this->year = $year;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    protected function _serializeToString(): string
    {
        $monthString = (string) $this->month;
        if (strlen($monthString) === 1) {
            $monthString = '0' . $monthString;
        }
        $serializedData = $this->year . '-' . $monthString;
        return $serializedData;
    }

    protected function _deserializeFromString(string $serializedData): void
    {
        if (\strlen($serializedData) !== 7) {
            throw new DeserializeException('The value serialized data string must have 7 characters', 1696227826);
        }
        $parts = \explode('-', $serializedData);
        if (\count($parts) !== 2) {
            throw new DeserializeException('The value serialized data must year und month to parts separate by -', 1696227896);
        }
        $year = (int) $parts[0];
        $month = (int) $parts[1];

        try {
            $this->setYear($year);
        } catch (InvalidArgumentException $exception) {
            throw new DeserializeException('Invalid year: ' . $exception->getMessage(), 1696228152, $exception);
        }

        try {
            $this->setMonth($month);
        } catch (InvalidArgumentException $exception) {
            throw new DeserializeException('Invalid month: ' . $exception->getMessage(), 1696228168, $exception);
        }
    }

    public function increment(): void
    {
        $this->month++;
        if ($this->month > 12) {
            $this->month = 1;
            $this->year++;
        }
    }

    public function compare(ComparableInterface $comparable): int
    {
        if ($comparable instanceof self === false) {
            throw new \Exception('sdhfdafgh');
        }

        if ($this->month === $comparable->month && $this->year === $comparable->year) {
            return 0;
        }

        return 9000;
    }
}
