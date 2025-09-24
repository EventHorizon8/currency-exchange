<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\ExchangeRateNotFoundException;
use App\Service\ConverterCurrencyService;
use App\Service\ExchangeRateService;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversMethod(ConverterCurrencyService::class, 'getRate')]
class ConverterRateCurrencyServiceTest extends TestCase
{
    private ConverterCurrencyService $service;
    private ExchangeRateService $exchangeRateService;

    protected function setUp(): void
    {
        $this->exchangeRateService = $this->createMock(ExchangeRateService::class);
        $this->service = new ConverterCurrencyService($this->exchangeRateService);
    }

    public function testGetRateFromCache(): void
    {
        $this->service = new ConverterCurrencyService($this->exchangeRateService, ['USD_EUR' => 0.85]);

        $rate = $this->service->getRate('USD', 'EUR');

        $this->assertSame(0.85, $rate);
    }

    public function testGetRateFromExchangeRateService(): void
    {
        $this->exchangeRateService
            ->expects($this->once())
            ->method('getLatestExchangeRate')
            ->with('USD', 'EUR')
            ->willReturn(0.85);

        $rate = $this->service->getRate('USD', 'EUR');

        $this->assertSame(0.85, $rate);
    }

    public function testGetRateThrowsExceptionWhenRateNotFound(): void
    {
        $this->exchangeRateService
            ->expects($this->once())
            ->method('getLatestExchangeRate')
            ->with('USD', 'EUR')
            ->willReturn(null);

        $this->expectException(ExchangeRateNotFoundException::class);
        $this->expectExceptionMessage('Exchange rate not found for USD to EUR');

        $this->service->getRate('USD', 'EUR');
    }
}
