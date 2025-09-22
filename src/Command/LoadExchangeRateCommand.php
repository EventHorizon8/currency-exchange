<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ExchangeRateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'currency:load-exchange-rate',
    description: 'Load exchange rates from external service.'
)]
class LoadExchangeRateCommand extends Command
{
    public function __construct(
        private readonly ExchangeRateService $exchangeRateService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Loading currency rates...');
        $this->exchangeRateService->loadAndSaveRates();
        $output->writeln('Currency rates loaded successfully.');
        return Command::SUCCESS;
    }
}
