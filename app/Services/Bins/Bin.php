<?php

declare(strict_types=1);

namespace App\Services\Bins;

use App\Services\Guzzle;
use GuzzleHttp\Exception\GuzzleException;

class Bin implements BinInterface
{
    private const string API_URL = 'https://lookup.binlist.net/';

    public function __construct(private readonly Guzzle $guzzle)
    {
    }

    public function getCountryCode(int $bin): ?string
    {
        $binData = $this->fetchFromAPI($bin);

        return $binData ? $this->extractCountryCode($binData) : null;
    }

    public function fetchFromAPI(int $bin): ?array
    {
        try {
            $response = $this->guzzle->get(self::API_URL . $bin);

            if (json_validate($response)) {
                $binData = json_decode($response, true);
            }

            return $binData ?? null;
        } catch (GuzzleException) {
            return null;
        }
    }

    public function extractCountryCode(array $binData): ?string
    {
        return $binData['country'] && $binData['country']['alpha2']
            ? $binData['country']['alpha2']
            : null ;
    }
}

