<?php

declare(strict_types=1);

namespace App\Services\Rates;

use App\Services\Guzzle;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRates implements RatesInterface
{
    private const string API_URL = 'https://api.exchangeratesapi.io/latest';
    private const string API_KEY = '4b7e43dbefd141ce03908dfe8780c139';
    private array $rates;

    /**
     * @throws GuzzleException
     */
    public function __construct(private readonly Guzzle $guzzle)
    {
        $this->setRates();
    }

    /**
     * @throws GuzzleException
     */
    public function setRates(): void
    {
        $APIRates = $this->fetchFromAPI();

        if ($APIRates && $APIRates['rates']) {
            $this->rates = $APIRates['rates'];
        }
    }

    /**
     * @throws GuzzleException
     */
    public function fetchFromAPI(): ?array
    {
        $response = $this->guzzle->get(self::API_URL . '?access_key=' . self::API_KEY);

        if (json_validate($response) && $result = json_decode($response, true)) {
            return $result;
        }

        return null;
    }

    public function find(string $currency): ?float
    {
        return $this->rates[$currency] ? (float)$this->rates[$currency] : null;
    }
}