<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ExchangeRate;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExchangeRateCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return ExchangeRate::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE, Action::BATCH_DELETE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Exchange Rate')
            ->setEntityLabelInPlural('Exchange Rates')
            ->setPageTitle('index', 'Exchange Rates')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        $currencies = $this->currencyRepository->findAll();
        $isoCodes = array_map(fn($currency) => $currency->getIsoCode(), $currencies);

        yield IdField::new('id');
        yield TextField::new('isoCode');
        yield TextField::new('baseCurrencyIso');
        yield NumberField::new('rate');
        yield TextField::new('status')
            ->setLabel('Status')
            ->setSortable(true)
            ->formatValue(function ($value, $entity) use ($isoCodes) {
                return in_array($entity->getIsoCode(), $isoCodes) ? 'Active' : '-';
            });
        yield DateTimeField::new('createdAt');
    }


    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $statusSort = $searchDto->getSort()['status'] ?? null;
        if ($statusSort) {
            // Remove the status sort from SearchDto to prevent the error
            $searchDto = new SearchDto(
                $searchDto->getRequest(),
                $searchDto->getSearchableProperties(),
                $searchDto->getQuery(),
                [],
                [],
                $searchDto->getAppliedFilters(),
            );
        }

        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($statusSort) {
            $queryBuilder
                ->addSelect('CASE WHEN c.id IS NOT NULL THEN 1 ELSE 0 END AS HIDDEN status')
                ->leftJoin('App\Entity\Currency', 'c', 'WITH', 'entity.isoCode = c.isoCode')
                ->addOrderBy('status', $statusSort);
        }
        return $queryBuilder;
    }
}
