<?php

declare(strict_types=1);

namespace tests;

use App\Services\Files\InvalidJsonList;
use Exception;
use PHPUnit\Framework\TestCase;

class InvalidJsonListTest extends TestCase
{
    private string $mockFilePath = '/tmp/mock_file.txt';

    protected function setUp(): void
    {
        parent::setUp();
        file_put_contents($this->mockFilePath, 'Test');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->mockFilePath)) {
            unlink($this->mockFilePath);
        }

        parent::tearDown();
    }

    public function testConstructorThrowsExceptionIfFileDoesNotExist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File /nonexistent/file/path.txt does not exist.');

        new InvalidJsonList('/nonexistent/file/path.txt');
    }

    public function testConstructorThrowsExceptionIfFileContentIsInvalid()
    {
        file_put_contents($this->mockFilePath, 'Invalid content');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File does not valid.');

        new InvalidJsonList($this->mockFilePath);
    }

    /**
     * @throws Exception
     */
    public function testToArrayReturnsCorrectData()
    {
        $validJsonContent = json_encode(['key1' => 'value1']) . PHP_EOL .  json_encode(['key2' => 'value2']);
        file_put_contents($this->mockFilePath, $validJsonContent);
        $invalidJsonList = new InvalidJsonList($this->mockFilePath);

        $expectedArray = [
            ['key1' => 'value1'],
            ['key2' => 'value2'],
        ];

        $this->assertEquals($expectedArray, $invalidJsonList->toArray());
    }
}