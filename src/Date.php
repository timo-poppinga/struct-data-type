<?php

declare(strict_types=1);

namespace Struct\DataType;

use InvalidArgumentException;
use Struct\Exception\Serialize\DeserializeException;

final class Date extends AbstractDataType
{
    protected int $year;

    protected int $month;

    protected int $day;

    public function setYear(int $year): void
    {
        if ($year < 1000 || $year > 9999) {
            throw new InvalidArgumentException('The year must be between 1000 and 9999', 1696052931);
        }
        $this->year = $year;
        $this->checkDay();
    }

    public function setMonth(int $month): void
    {
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('The month must be between 1 and 12', 1696052867);
        }
        $this->month = $month;
        $this->checkDay();
    }

    public function setDay(int $day): void
    {
        if ($day < 1 || $day > 31) {
            throw new InvalidArgumentException('The day must be between 1 and 31', 1696052931);
        }
        $this->day = $day;
        $this->checkDay();
    }

    public function setDate(int $year, int $month, int $day): void
    {
        $this->setYear($year);
        $this->setMonth($month);
        $this->setDay($day);
    }

    protected function checkDay(): void
    {
        if (isset($this->year) === false) {
            return;
        }
        if (isset($this->month) === false) {
            return;
        }
        if (isset($this->day) === false) {
            return;
        }
        $checkDate = new \DateTime($this->year . '-' . $this->month . '-01', new \DateTimeZone('UTC'));
        $checkDate->setTime(0, 0);
        $numberOfDays = (int) $checkDate->format('t');
        if ($this->day > $numberOfDays) {
            throw new InvalidArgumentException('The month: ' . $this->month . ' in the year: ' . $this->year . ' has only: ' . $numberOfDays . ' days. Given: ' . $this->day, 1696334057);
        }
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    protected function _serializeToString(): string
    {
        $yearString = (string) $this->year;
        $monthString = (string) $this->month;
        $dayString = (string) $this->day;
        if (strlen($monthString) === 1) {
            $monthString = '0' . $monthString;
        }
        if (strlen($dayString) === 1) {
            $dayString = '0' . $dayString;
        }
        $serializedData = $yearString . '-' . $monthString . '-' . $dayString;
        return $serializedData;
    }

    protected function _deserializeFromString(string $serializedData): void
    {
        if (\strlen($serializedData) !== 10) {
            throw new DeserializeException('The value serialized data string must have 10 characters', 1696334669);
        }
        $parts = \explode('-', $serializedData);
        if (\count($parts) !== 3) {
            throw new DeserializeException('The value serialized data must have year, month and day separate by -', 1696334753);
        }
        $year = (int) $parts[0];
        $month = (int) $parts[1];
        $day = (int) $parts[2];

        try {
            $this->setYear($year);
        } catch (InvalidArgumentException $exception) {
            throw new DeserializeException('Invalid year: ' . $exception->getMessage(), 1696334757, $exception);
        }

        try {
            $this->setMonth($month);
        } catch (InvalidArgumentException $exception) {
            throw new DeserializeException('Invalid month: ' . $exception->getMessage(), 1696334760, $exception);
        }

        try {
            $this->setDay($day);
        } catch (InvalidArgumentException $exception) {
            throw new DeserializeException('Invalid day: ' . $exception->getMessage(), 1696334763, $exception);
        }
    }
}
