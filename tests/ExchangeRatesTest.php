<?php

declare(strict_types=1);

namespace tests;

use App\Services\Guzzle;
use App\Services\Rates\ExchangeRates;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ExchangeRatesTest extends TestCase
{
    private const array RATES = [
        'USD' => 1.2,
        'EUR' => 1.0,
    ];
    private Guzzle $guzzleMock;
    private ExchangeRates $exchangeRates;

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->guzzleMock = $this->createMock(Guzzle::class);
        $this->exchangeRates = new ExchangeRates($this->guzzleMock);
    }

    /**
     * @throws GuzzleException
     */
    public function testSetRatesAndFindCurrency()
    {
        $mockApiResponse = json_encode(['rates' => self::RATES]);

        $this->guzzleMock->method('get')
            ->willReturn($mockApiResponse);

        $this->exchangeRates->setRates();

        $this->assertEquals(self::RATES['USD'], $this->exchangeRates->find('USD'), 'Should find rate for USD');
        $this->assertEquals(self::RATES['EUR'], $this->exchangeRates->find('EUR'), 'Should find rate for EUR');
    }

    /**
     * @throws GuzzleException
     */
    public function testFindNonExistingCurrency()
    {
        $mockApiResponse = json_encode(['rates' => self::RATES]);

        $this->guzzleMock->method('get')
            ->willReturn($mockApiResponse);

        $this->exchangeRates->setRates();

        $this->assertNull($this->exchangeRates->find('GBP'), 'Should return null for non-existing currency');
    }

    /**
     * @throws GuzzleException
     */
    public function testFetchFromAPISuccess()
    {
        $mockApiResponse = json_encode(['rates' => self::RATES]);

        $this->guzzleMock->method('get')
            ->willReturn($mockApiResponse);

        $result = $this->exchangeRates->fetchFromAPI();

        $expected = ['rates' => self::RATES];
        $this->assertEquals($expected, $result);
    }

    /**
     * @throws GuzzleException
     */
    public function testFetchFromAPIThrowsException()
    {
        $this->guzzleMock->method('get')
            ->willThrowException(new RequestException('Network error', new Request('GET', 'test')));

        $this->expectException(RequestException::class);
        $this->exchangeRates->fetchFromAPI();
    }
}