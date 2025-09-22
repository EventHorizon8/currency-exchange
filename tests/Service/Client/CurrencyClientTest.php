<?php

declare(strict_types=1);

namespace App\Tests\Service\Client;

use App\Service\Client\CurrencyClient;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[CoversClass(CurrencyClient::class)]
class CurrencyClientTest extends TestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetRatesReturnsExpectedArray()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $httpClient->method('request')->willReturn($response);
        $response->method('toArray')->willReturn([
            'data' => ['USD' => 1.1, 'EUR' => 0.9]
        ]);

        $client = new CurrencyClient($httpClient, 'test-key', 'http://api.url');
        $result = $client->getRates('USD', ['EUR']);

        $this->assertIsArray($result);
        $this->assertEquals(['USD' => 1.1, 'EUR' => 0.9], $result);
    }

    /**
     * @throws Exception
     */
    public function testGetRatesThrowsExceptionOnMissingConfig()
    {
        $this->expectException(InvalidArgumentException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        new CurrencyClient($httpClient, '', '');
    }
}
