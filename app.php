<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Enums\EuropeanCountryCode;
use App\Services\Bins\Bin;
use App\Services\Convertor;
use App\Services\Files\InvalidJsonList;
use App\Services\Guzzle;
use App\Services\Rates\ExchangeRates;

try {
    $transactionList = new InvalidJsonList($argv[1]);
    $binService = new Bin(new Guzzle());
    $ratesService = new ExchangeRates(new Guzzle());
    $convertor = new Convertor();

    foreach ($transactionList->toArray() as $transaction) {
        $countryCode = $binService->getCountryCode((int)$transaction['bin']);

        if (!$countryCode) {
            echo 'Wrong country code(or API response error) for bin : ' . $transaction['bin'] . PHP_EOL;
            continue;
        }

        $currencyRate = $ratesService->find($transaction['currency']);

        if (!$currencyRate) {
            echo 'Rate does not found for currency: ' . $transaction['currency'] . PHP_EOL;
            continue;
        }

        echo $convertor->calculate(
            $transaction,
            $currencyRate,
            EuropeanCountryCode::exists($countryCode),
        ) . PHP_EOL;
    }
} catch (Throwable $exception) {
    echo $exception->getMessage();
}