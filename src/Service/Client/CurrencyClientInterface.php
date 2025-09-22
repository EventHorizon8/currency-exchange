<?php

declare(strict_types=1);

namespace App\Service\Client;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Interface for currency client implementations.
 */
interface CurrencyClientInterface
{
    #[ArrayShape(['currency_code' => 'float'])]
    public function getRates(string $baseCurrency, array $targetCurrencies = []): array;

}
