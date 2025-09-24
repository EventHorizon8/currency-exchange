<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    private const int DEFAULT_LIMIT = 10;

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

    /**
     * Find the latest exchange rates for the given ISO codes.
     * @param array $isoCodes
     * @return array
     */
    public function findLatestRatesByIso(array $isoCodes): array
    {
        $sqlQuery = 'SELECT DISTINCT ON (iso_code)
                   id,
                   iso_code,
                   base_currency_iso,
                   rate,
                   created_at
            FROM exchange_rate
            WHERE iso_code IN (:isoCodes)
            ORDER BY iso_code, created_at DESC';


        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(ExchangeRate::class, 'er');

        $query = $this->getEntityManager()
            ->createNativeQuery($sqlQuery, $rsm);
        $query->setParameters([
            'isoCodes' => array_map(fn($isoCode) => strtoupper($isoCode), $isoCodes),
        ]);
        return $query->getResult();
    }

    /**
     * Find the latest active exchange rates, which set in limited by the specified number.
     * @param int $limit
     * @return array
     */
    public function findLatestActiveRates(int $limit = self::DEFAULT_LIMIT): array
    {
        return $this->createQueryBuilder('er')
            ->innerJoin('App\Entity\Currency', 'c', 'WITH', 'er.isoCode = c.isoCode')
            ->orderBy('er.createdAt', 'DESC')
            ->addOrderBy('er.isoCode', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
