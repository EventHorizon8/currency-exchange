<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\Service\Client\CurrencyClientInterface;
use App\Service\ExchangeRateService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    private ExchangeRateRepository $exchangeRateRepository;
    private ExchangeRateService $service;

    protected function setUp(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $currencyClient = $this->createMock(CurrencyClientInterface::class);
        $this->exchangeRateRepository = $this->createMock(ExchangeRateRepository::class);
        $this->service = new ExchangeRateService(
            $em,
            $currencyClient,
            $this->exchangeRateRepository,
            'USD'
        );
    }

    public function testSameCurrencyReturnsOne(): void
    {
        $this->assertSame(1.0, $this->service->getLatestExchangeRate('USD', 'USD'));
    }

    public function testNoRateFoundReturnsNull(): void
    {
        $this->exchangeRateRepository
            ->method('findLatestRatesByIso')
            ->willReturn([]);

        $this->assertNull($this->service->getLatestExchangeRate('USD', 'EUR'));
    }

    public function testFromBaseCurrency(): void
    {
        $rate = $this->createMock(ExchangeRate::class);
        $rate->method('getRate')->willReturn(0.9);
        $rate->method('getIsoCode')->willReturn('EUR');

        $this->exchangeRateRepository
            ->method('findLatestRatesByIso')
            ->with($this->callback(function (array $isoCodes) {
                return in_array('EUR', $isoCodes, true);
            }))
            ->willReturn([$rate]);

        $this->assertSame(0.9, $this->service->getLatestExchangeRate('USD', 'EUR'));
    }

    public function testToBaseCurrency(): void
    {
        $rate = $this->createMock(ExchangeRate::class);
        $rate->method('getRate')->willReturn(0.8);
        $rate->method('getIsoCode')->willReturn('GBP');

        $this->exchangeRateRepository
            ->method('findLatestRatesByIso')
            ->with($this->callback(function (array $isoCodes) {
                return in_array('GBP', $isoCodes, true);
            }))
            ->willReturn([$rate]);

        $this->assertSame(1 / 0.8, $this->service->getLatestExchangeRate('GBP', 'USD'));
    }

    public function testCrossCurrency(): void
    {
        $eurRate = $this->createMock(ExchangeRate::class);
        $eurRate->method('getRate')->willReturn(0.9);
        $eurRate->method('getIsoCode')->willReturn('EUR');

        $gbpRate = $this->createMock(ExchangeRate::class);
        $gbpRate->method('getRate')->willReturn(0.8);
        $gbpRate->method('getIsoCode')->willReturn('GBP');

        $this->exchangeRateRepository
            ->method('findLatestRatesByIso')
            ->willReturn([
                $eurRate,
                $gbpRate,
            ]);

        $this->assertSame(0.9 / 0.8, $this->service->getLatestExchangeRate('GBP', 'EUR'));
    }
}
