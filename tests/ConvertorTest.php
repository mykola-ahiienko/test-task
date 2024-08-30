<?php

declare(strict_types=1);

namespace tests;

use App\Services\Convertor;
use PHPUnit\Framework\TestCase;

class ConvertorTest extends TestCase
{
    private Convertor $convertor;

    protected function setUp(): void
    {
        $this->convertor = new Convertor();
    }

    public function testCalculateWithDefaultCurrency()
    {
        $transaction = ['amount' => 100, 'currency' => 'EUR'];
        $currencyRate = 1.0;

        $result = $this->convertor->calculate($transaction, $currencyRate, true);

        $expected = round($transaction['amount'] * Convertor::EU_COEFFICIENT, 2);
        $this->assertEquals($expected, $result);
    }

    public function testCalculateWithNonDefaultCurrencyAndEuropeanCountry()
    {
        $transaction = ['amount' => 100, 'currency' => 'USD'];
        $currencyRate = 1.2;

        $result = $this->convertor->calculate($transaction, $currencyRate, true);

        $expected = round(($transaction['amount'] / $currencyRate) * Convertor::EU_COEFFICIENT, 2);
        $this->assertEquals($expected, $result);
    }

    public function testCalculateWithNonDefaultCurrencyAndNonEuropeanCountry()
    {
        $transaction = ['amount' => 100, 'currency' => 'USD'];
        $currencyRate = 1.2;

        $result = $this->convertor->calculate($transaction, $currencyRate, false);

        $expected = round(($transaction['amount'] / $currencyRate) * Convertor::DEFAULT_COEFFICIENT, 2);
        $this->assertEquals($expected, $result);
    }

    public function testCalculateWithZeroCurrencyRate()
    {
        $transaction = ['amount' => 100, 'currency' => 'USD'];
        $currencyRate = 0;

        $result = $this->convertor->calculate($transaction, $currencyRate, false);

        $expected = round($transaction['amount'] * Convertor::DEFAULT_COEFFICIENT,2);
        $this->assertEquals($expected, $result);
    }
}