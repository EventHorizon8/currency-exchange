<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\ExchangeRateNotFoundException;
use App\Service\ConverterCurrencyService;
use App\Service\ExchangeRateService;
use PHPUnit\Framework\TestCase;

class ConverterCurrencyServiceTest extends TestCase
{
    public function testConvertReturnsRoundedAmount(): void
    {
        $exchangeRateService = $this->createMock(ExchangeRateService::class);
        $exchangeRateService->method('getLatestExchangeRate')
            ->with('USD', 'EUR')
            ->willReturn(0.91);

        $service = new ConverterCurrencyService($exchangeRateService);

        $result = $service->convert(100, 'USD', 'EUR');
        $this->assertSame(91.0, $result);
    }

    public function testConvertThrowsExceptionWhenRateNotFound(): void
    {
        $exchangeRateService = $this->createMock(ExchangeRateService::class);
        $exchangeRateService->method('getLatestExchangeRate')
            ->with('USD', 'JPY')
            ->willReturn(null);

        $service = new ConverterCurrencyService($exchangeRateService);

        $this->expectException(ExchangeRateNotFoundException::class);
        $service->convert(100, 'USD', 'JPY');
    }
}
