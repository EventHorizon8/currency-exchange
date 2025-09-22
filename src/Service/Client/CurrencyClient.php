<?php

declare(strict_types=1);

namespace App\Service\Client;

use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * CurrencyClient is responsible for fetching currency exchange rates from an external API.
 */
readonly class CurrencyClient implements CurrencyClientInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private ?string $authKey = '',
        private ?string $url = ''
    ) {
        if (empty($this->authKey) || empty($this->url)) {
            throw new InvalidArgumentException('authKey and url must be provided for CurrencyClient.');
        }
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[ArrayShape(['currency_code' => 'float'])]
    public function getRates(string $baseCurrency, array $targetCurrencies = []): array
    {
        $response = $this->client->request(
            'GET',
            $this->url,
            [
                'query' => array_merge([
                    'base_currency' => $baseCurrency,
                ],  $targetCurrencies ? ['currencies' => implode(',', $targetCurrencies)] : []),
                'headers' => [
                    'Accept' => 'application/json',
                    'apikey' => $this->authKey,
                ],
            ]
        );
        return $response->toArray()['data'] ?? [];
    }
}
