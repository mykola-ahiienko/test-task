<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Guzzle
{
    private const string METHOD_GET = 'GET';

    /**
     * @throws GuzzleException
     */
    public function get(string $url, array $headers = []): string
    {
        $response = (new Client())->request(self::METHOD_GET, $url, [
            'headers' => $headers,
        ]);

        return $response->getBody()->getContents();
    }
}