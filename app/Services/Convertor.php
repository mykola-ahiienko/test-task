<?php

declare(strict_types=1);

namespace App\Services;

class Convertor
{
    private const string DEFAULT_CURRENCY = 'EUR';
    private const float ZERO = 0;
    public const float EU_COEFFICIENT = 0.01;
    public const float DEFAULT_COEFFICIENT = 0.02;

    public function calculate(array $transaction, float $currencyRate, bool $isEuropeanCountry): float
    {
        $finalAmount = $transaction['amount'];

        if (!$this->isDefaultCurrency($transaction['currency']) || $currencyRate > self::ZERO) {
            if ($currencyRate !== self::ZERO) {
                $finalAmount = $transaction['amount'] / $currencyRate;
            }
        }

        return round(
            $finalAmount * ($isEuropeanCountry ? self::EU_COEFFICIENT : self::DEFAULT_COEFFICIENT),
            2,
        );
    }

    private function isDefaultCurrency(string $currency): bool
    {
        return $currency === self::DEFAULT_CURRENCY;
    }
}