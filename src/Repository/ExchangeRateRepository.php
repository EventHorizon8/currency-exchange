<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    /**
     * Find the latest exchange rate from one currency to another.
     * @param string $targetCurrency
     * @return ExchangeRate|null
     */
    public function findLatestRate(string $targetCurrency): ?ExchangeRate
    {
        return $this->createQueryBuilder('er')
            ->where('er.isoCode = :targetCurrency')
            ->setParameter('targetCurrency', strtoupper($targetCurrency))
            ->orderBy('er.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
