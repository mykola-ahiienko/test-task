<?php

declare(strict_types=1);

namespace tests;

use App\Enums\EuropeanCountryCode;
use App\Services\Bins\Bin;
use App\Services\Convertor;
use App\Services\Files\InvalidJsonList;
use App\Services\Rates\ExchangeRates;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testApp()
    {
        $mockTransactionList = $this->createMock(InvalidJsonList::class);
        $mockBinService = $this->createMock(Bin::class);
        $mockRatesService = $this->createMock(ExchangeRates::class);
        $mockConvertor = $this->createMock(Convertor::class);

        $transactions = [
            ['bin' => '123456', 'currency' => 'USD', 'amount' => 100],
            ['bin' => '654321', 'currency' => 'EUR', 'amount' => 200],
        ];

        $mockTransactionList->method('toArray')->willReturn($transactions);

        $mockBinService->method('getCountryCode')
            ->willReturnMap([
                [123456, 'DE'],
                [654321, 'FR'],
            ]);

        $mockRatesService->method('find')
            ->willReturnMap([
                ['USD', 1.1],
                ['EUR', 1.0],
            ]);

        $mockConvertor->method('calculate')
            ->willReturnMap([
                [$transactions[0], 1.1, true, 110.0],
                [$transactions[1], 1.0, true, 200.0],
            ]);

        foreach ($transactions as $index => $transaction) {
            $countryCode = $mockBinService->getCountryCode((int)$transaction['bin']);
            $this->assertNotEmpty($countryCode, 'Country code should not be empty');

            $currencyRate = $mockRatesService->find($transaction['currency']);
            $this->assertNotEmpty($currencyRate, 'Currency rate should not be empty');

            $result = $mockConvertor->calculate(
                $transaction,
                $currencyRate,
                EuropeanCountryCode::exists($countryCode),
            );


            $expectedResults = [110.0, 200.0];
            $this->assertEquals($expectedResults[$index], $result, 'Conversion result is not as expected');
        }
    }
}