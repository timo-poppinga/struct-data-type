<?php

declare(strict_types=1);

namespace Struct\DataType;

use Struct\DataType\Enum\AmountVolume;
use Struct\DataType\Enum\Currency;
use Struct\Exception\Serialize\DeserializeException;

final class Amount extends AbstractDataType
{
    protected int $value;
    protected Currency $currency = Currency::EUR;
    protected AmountVolume $amountVolume = AmountVolume::Base;
    protected int $decimals = 2;

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmountVolume(): AmountVolume
    {
        return $this->amountVolume;
    }

    public function setAmountVolume(AmountVolume $amountVolume): void
    {
        $this->amountVolume = $amountVolume;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function setDecimals(int $decimals): void
    {
        $this->decimals = $decimals;
    }

    public function setAmount(
        int $value,
        Currency $currency = Currency::EUR,
        AmountVolume $amountVolume = AmountVolume::Base,
        int $decimals = 2
    ): void {
        $this->value = $value;
        $this->currency = $currency;
        $this->amountVolume = $amountVolume;
        $this->decimals = $decimals;
    }

    protected function _deserializeFromString(string $serializedData): void
    {
        $negativ = false;
        if (\str_starts_with($serializedData, '-')) {
            $negativ = true;
            $serializedData = substr($serializedData, 1);
        }

        $parts = \explode(' ', $serializedData);
        if (count($parts) !== 2) {
            throw new DeserializeException('The amount and currency must be separated by a space', 1696314552);
        }

        $amountString = $parts[0];
        $currencyCode = $parts[1];

        $amountVolumeCharacter = '';
        if (\strlen($currencyCode) === 4) {
            $amountVolumeCharacter = \substr($currencyCode, 0, 1);
            $currencyCode = \substr($currencyCode, 1);
        }

        $amountVolume = AmountVolume::tryFrom($amountVolumeCharacter);
        $currency = null;
        $cases = Currency::cases();
        foreach ($cases as $case) {
            if ($case->name === $currencyCode) {
                $currency = $case;
            }
        }

        if ($amountVolume === null) {
            throw new DeserializeException('The amount value is invalid: ' . $amountVolumeCharacter, 1696315092);
        }
        if ($currency === null) {
            throw new DeserializeException('The currency code is invalid: ' . $currencyCode, 1696315127);
        }
        $amountParts = \explode('.', $amountString);
        if (\count($amountParts) > 2) {
            throw new DeserializeException('The amount must not have more than one decimal: ' . $amountString, 1696315411);
        }
        $amountFull = $amountParts[0];
        $amountFraction = '';
        if (\count($amountParts) === 2) {
            $amountFraction = $amountParts[1];
        }

        $decimals = \strlen($amountFraction);
        $amount = $amountFull . $amountFraction;
        $value = (int) $amount;

        while (\str_starts_with($amount, '0')) {
            $amount = substr($amount, 1);
        }

        if ($amount === '') {
            $amount = '0';
        }

        if ((string) $value !== $amount) {
            throw new DeserializeException('Invalid character in amount: ' . $amountString, 1696315612);
        }

        if ($negativ === true) {
            $value *= -1;
        }

        $this->value = $value;
        $this->decimals = $decimals;
        $this->currency = $currency;
        $this->amountVolume = $amountVolume;
    }

    protected function _serializeToString(): string
    {
        $value = $this->value;
        $decimals = $this->decimals;
        $negativ = false;
        if ($value < 0) {
            $negativ = true;
            $value *= -1;
        }

        $amount = '';
        if ($negativ === true) {
            $amount .= '-';
        }

        $valueString = (string) $value;
        if ($decimals > 0) {
            while (\strlen($valueString) <= $decimals) {
                $valueString = '0' . $valueString;
            }
            $amount .= \substr($valueString, 0, $decimals * -1);
            $amount .= '.';
            $amount .= \substr($valueString, $decimals * -1);
        } else {
            $amount .= $valueString;
        }

        $amount .= ' ';
        $amount .= $this->amountVolume->value;
        $amount .= $this->currency->name;
        return $amount;
    }
}
