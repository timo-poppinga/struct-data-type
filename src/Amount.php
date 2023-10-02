<?php

declare(strict_types=1);

namespace Struct\DataType;


final class Amount extends AbstractDataType
{
    protected int $value;

    protected int $decimals = 2;

    protected string $currency = 'EUR';

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function setDecimals(int $decimals): void
    {
        $this->decimals = $decimals;
    }



    protected function _deserializeToString(string $serializedData): void
    {
       $this->value = (int) $serializedData;
    }

    protected function _serializeToString(): string
    {
        $value = $this->value;
        $decimals = $this->decimals;
        $negativ = false;
        if($value < 0) {
            $negativ = true;
            $value *= -1;
        }

        $valueString = (string) $value;
        while (\strlen($valueString) <= $decimals) {
            $valueString = '0'. $valueString;
        }
        $amount = '';
        if($negativ === true) {
            $amount .= '-';
        }
        $amount .= \substr($valueString, 0,  $decimals * -1);
        $amount .= '.';
        $amount .= \substr($valueString, $decimals * -1);

        $amount .= ' ' . $this->currency;
        return $amount;
    }
}
