<?php

namespace App\Controller\Admin;

use App\Entity\ActionStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class ActionStatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActionStatus::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('descrizione')->setLabel('Descrizione'),
            IntegerField::new('ordine')->setLabel('Ordine'),
            BooleanField::new('chiusura')->setLabel('È stato di chiusura?'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Stato azione')
            ->setEntityLabelInPlural('Tipologie stati')
            ->setPageTitle(Crud::PAGE_INDEX, 'Tipologie stati')
            ->setDefaultSort(['ordine' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $a) => $a->setLabel('Nuovo stato'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(BooleanFilter::new('chiusura')->setLabel('Stato di chiusura'));
    }
}