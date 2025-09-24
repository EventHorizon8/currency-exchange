<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Repository\CurrencyRepository;
use App\Repository\ExchangeRateRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private const RECENT_RATES_LIMIT = 10;

    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly ExchangeRateRepository $exchangeRateRepository,
    )
    {
    }

    public function index(): Response
    {
        $currencies = $this->currencyRepository->findAll();
        usort($currencies, fn($a, $b) => $a->getIsoCode() <=> $b->getIsoCode());

        return $this->render(
            'admin/dashboard.html.twig',
            [
                'currencies' => $currencies,
                'recentRates' => $this->exchangeRateRepository->findLatestActiveRates(self::RECENT_RATES_LIMIT),
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Currency Exchange');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Currencies', 'fa fa-coins', Currency::class)
            ->setController(CurrencyCrudController::class);
        yield MenuItem::linkToCrud('Exchange Rates', 'fa fa-chart-bar', ExchangeRate::class)
            ->setController(ExchangeRateCrudController::class);

        yield MenuItem::section('Tools');

        yield MenuItem::linkToRoute('Rate Calculator', 'fa fa-calculator', 'admin')
            ->setLinkRel('noopener');
    }
}
