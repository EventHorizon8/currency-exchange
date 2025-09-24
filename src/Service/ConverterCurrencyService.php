<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ExchangeRateNotFoundException;

class ConverterCurrencyService
{

    public function __construct(
        private readonly ExchangeRateService $exchangeRateService,
        private array $ratesCache = []
    ) {
    }

    /**
     * Convert an amount from one currency to another using the latest exchange rates.
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     * @throws ExchangeRateNotFoundException
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $rate = $this->getRate($fromCurrency, $toCurrency);
        return round($amount * $rate, 2);
    }

    /**
     * get rate by currency pair
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     * @throws ExchangeRateNotFoundException
     */
    public function getRate(string $fromCurrency, string $toCurrency): float
    {
        $currencyKey = strtoupper($fromCurrency) . '_' . strtoupper($toCurrency);

        if(!isset($this->ratesCache[$currencyKey])) {
            $rate = $this->exchangeRateService->getLatestExchangeRate($fromCurrency, $toCurrency);
            if ($rate === null) {
                throw new ExchangeRateNotFoundException("Exchange rate not found for $fromCurrency to $toCurrency");
            }

            $this->ratesCache[$currencyKey] = $rate;
        }

        return $this->ratesCache[$currencyKey];
    }
}
