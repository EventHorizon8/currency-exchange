<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Currency;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CurrencyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Currency::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Currency')
            ->setEntityLabelInPlural('Currencies')
            ->setPageTitle('index', 'Currencies Management')
            ->setDefaultSort(['isoCode' => 'ASC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('isoCode')
                ->setLabel('Currency Code')
                ->setHelp('3-letter ISO currency code (e.g., USD, EUR)')
                ->setRequired(true)
                ->setColumns(2)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 3,
                        'minlength' => 3,
                        'pattern' => '[A-Za-z]{3}',
                        'title' => 'Please enter exactly 3 letters',
                        'style' => 'text-transform: uppercase;',
                        'oninput' => 'this.value = this.value.toUpperCase().replace(/[^A-Z]/g, "").substring(0, 3);'
                    ]
                ]),
            TextField::new('name')
                ->setLabel('Currency Name')
                ->setHelp('Full name of the currency')
                ->setRequired(true)
                ->setColumns(6)
                ->setFormTypeOptions([
                    'attr' => [
                        'maxlength' => 255,
                        'title' => 'Please enter max 255 letters',
                    ]
                ]),
        ];
    }
}
