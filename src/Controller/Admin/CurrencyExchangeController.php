<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\ConverterCurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CurrencyExchangeController extends AbstractController
{
    public function __construct(
        private readonly ConverterCurrencyService $converter,
    )
    {
    }

    #[Route('/currency-exchange/convert', name: 'admin_currency_exchange_convert', methods: ['POST'])]
    public function convert(Request $request): Response
    {
        $amount = (float)$request->getPayload()->get('amount', 0.0);
        $fromCurrencyIso = $request->getPayload()->get('fromCurrencyIso');
        $toCurrencyIso = $request->getPayload()->get('toCurrencyIso');

        $result = $this->converter->convert($amount, $fromCurrencyIso, $toCurrencyIso);
        $rate = $this->converter->getRate($fromCurrencyIso, $toCurrencyIso);

        return $this->json([
            'originalAmount' => $amount,
            'fromCurrencyIso' => $fromCurrencyIso,
            'convertedAmount' => $result,
            'toCurrencyIso' => $toCurrencyIso,
            'rate' => $rate,
        ]);
    }
}
