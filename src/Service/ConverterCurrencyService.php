<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ExchangeRateNotFoundException;

readonly class ConverterCurrencyService
{

    public function __construct(
        private ExchangeRateService $exchangeRateService
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
        $rate = $this->exchangeRateService->getLatestExchangeRate($fromCurrency, $toCurrency);
        if ($rate === null) {
            throw new ExchangeRateNotFoundException("Exchange rate not found for $fromCurrency to $toCurrency");
        }
        return round($amount * $rate, 2);
    }
}
