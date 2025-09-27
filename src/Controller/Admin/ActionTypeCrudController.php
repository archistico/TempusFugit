<?php

namespace App\Controller\Admin;

use App\Entity\ActionType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class ActionTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActionType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('descrizione')->setLabel('Descrizione'),
            TextField::new('colore')
                ->setLabel('Colore (hex)')
                ->setHelp('Es. #3b82f6')
                ->hideOnIndex(),
            TextField::new('icona')
                ->setLabel('Icona (nome opzionale)')
                ->setHelp('Es. "code", "users"…')
                ->hideOnIndex(),
            BooleanField::new('fatturabileDefault')->setLabel('Fatturabile di default?'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tipologia azione')
            ->setEntityLabelInPlural('Tipologie azioni')
            ->setPageTitle(Crud::PAGE_INDEX, 'Tipologie azioni')
            ->setDefaultSort(['descrizione' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $a) => $a->setLabel('Nuova tipologia'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('descrizione')->setLabel('Descrizione'))
            ->add(BooleanFilter::new('fatturabileDefault')->setLabel('Fatturabile di default'));
    }
}