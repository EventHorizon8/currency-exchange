<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\Service\Client\CurrencyClientInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class ExchangeRateService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CurrencyClientInterface $currencyClient,
        private ExchangeRateRepository $exchangeRateRepository,
        private string $baseCurrency,
    )
    {
    }

    /**
     * Load exchange rates from external service and save them to the database.
     */
    public function loadAndSaveRates(): void
    {
        $result = $this->currencyClient->getRates($this->baseCurrency);

        foreach ($result as $code => $rate) {
            $exchangeRate = new ExchangeRate();
            $exchangeRate->setIsoCode($code)
                ->setRate($rate)
                ->setBaseCurrencyIso($this->baseCurrency)
                ->updateCreatedDateTime();
            $this->em->persist($exchangeRate);
        }
        $this->em->flush();
    }

    /**
     * Get the latest exchange rate from one currency to another.
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float|null
     */
    public function getLatestExchangeRate(string $fromCurrency, string $toCurrency): ?float
    {
        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $toExchangeRate = null;
        if ($toCurrency !== $this->baseCurrency) {
            $toExchangeRate = $this->exchangeRateRepository->findLatestRate($toCurrency);
            if ($toExchangeRate === null) {
                return null;
            }
        }

        $fromExchangeRate = null;
        if ($fromCurrency !== $this->baseCurrency) {
            $fromExchangeRate = $this->exchangeRateRepository->findLatestRate($fromCurrency);
            if ($fromExchangeRate === null) {
                return null;
            }
        }

        if ($fromCurrency === $this->baseCurrency) {
            return $toExchangeRate->getRate();
        }
        if ($toCurrency === $this->baseCurrency) {
            return 1 / $fromExchangeRate->getRate();
        }

        return $toExchangeRate->getRate() / $fromExchangeRate->getRate();
    }


}
