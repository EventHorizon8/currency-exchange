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
    private EntityManagerInterface $em;
    private CurrencyClientInterface $currencyClient;
    private ExchangeRateRepository $exchangeRateRepository;
    private ExchangeRateService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->currencyClient = $this->createMock(CurrencyClientInterface::class);
        $this->exchangeRateRepository = $this->createMock(ExchangeRateRepository::class);
        $this->service = new ExchangeRateService(
            $this->em,
            $this->currencyClient,
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
            ->method('findLatestRate')
            ->willReturn(null);

        $this->assertNull($this->service->getLatestExchangeRate('USD', 'EUR'));
    }

    public function testFromBaseCurrency(): void
    {
        $rate = $this->createMock(ExchangeRate::class);
        $rate->method('getRate')->willReturn(0.9);

        $this->exchangeRateRepository
            ->method('findLatestRate')
            ->with('EUR')
            ->willReturn($rate);

        $this->assertSame(0.9, $this->service->getLatestExchangeRate('USD', 'EUR'));
    }

    public function testToBaseCurrency(): void
    {
        $rate = $this->createMock(ExchangeRate::class);
        $rate->method('getRate')->willReturn(0.8);

        $this->exchangeRateRepository
            ->method('findLatestRate')
            ->with('GBP')
            ->willReturn($rate);

        $this->assertSame(1 / 0.8, $this->service->getLatestExchangeRate('GBP', 'USD'));
    }

    public function testCrossCurrency(): void
    {
        $eurRate = $this->createMock(ExchangeRate::class);
        $eurRate->method('getRate')->willReturn(0.9);

        $gbpRate = $this->createMock(ExchangeRate::class);
        $gbpRate->method('getRate')->willReturn(0.8);

        $this->exchangeRateRepository
            ->method('findLatestRate')
            ->willReturnMap([
                ['EUR', $eurRate],
                ['GBP', $gbpRate],
            ]);

        $this->assertSame(0.9 / 0.8, $this->service->getLatestExchangeRate('GBP', 'EUR'));
    }
}
