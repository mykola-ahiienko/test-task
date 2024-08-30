<?php

declare(strict_types=1);

namespace tests;

use App\Services\Bins\Bin;
use App\Services\Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class BinTest extends TestCase
{
    private Guzzle $guzzleMock;
    private Bin $bin;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->guzzleMock = $this->createMock(Guzzle::class);
        $this->bin = new Bin($this->guzzleMock);
    }

    public function testGetCountryCodeSuccess()
    {
        $mockApiResponse = json_encode([
            'country' => [
                'alpha2' => 'US',
            ],
        ]);

        $this->guzzleMock->method('get')
            ->willReturn($mockApiResponse);

        $result = $this->bin->getCountryCode(45717360);

        $this->assertEquals('US', $result, 'Should return correct country code');
    }

    public function testGetCountryCodeInvalidResponse()
    {
        $invalidApiResponse = 'Invalid JSON response';

        $this->guzzleMock->method('get')
            ->willReturn($invalidApiResponse);

        $result = $this->bin->getCountryCode(45717360);

        $this->assertNull($result, 'Should return null for invalid JSON response');
    }

    public function testGetCountryCodeThrowsException()
    {
        $this->guzzleMock->method('get')
            ->willThrowException(new RequestException('Network error', new Request('GET', 'test')));

        $result = $this->bin->getCountryCode(45717360);

        $this->assertNull($result, 'Should return null when exception is thrown');
    }

    public function testExtractCountryCodeValidData()
    {
        $binData = [
            'country' => [
                'alpha2' => 'US',
            ],
        ];

        $result = $this->bin->extractCountryCode($binData);

        $this->assertEquals('US', $result, 'Should extract correct country code');
    }

    public function testExtractCountryCodeInvalidData()
    {
        $binData = [
            'country' => [],
        ];

        $result = $this->bin->extractCountryCode($binData);

        $this->assertNull($result, 'Should return null for missing country data');
    }
}