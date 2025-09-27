<?php

namespace App\Controller\Admin;

use App\Entity\TimeEntry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class TimeEntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TimeEntry::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // Durata formattata (solo lettura nelle liste/dettagli)
        $durataIndex = IntegerField::new('durataMin', 'Durata')
            ->onlyOnIndex()
            ->formatValue(function ($v) {
                if (!is_numeric($v)) return '';
                $h = intdiv((int)$v, 60);
                $m = (int)$v % 60;
                return sprintf('%02d:%02d', $h, $m);
            });

        $durataDetail = IntegerField::new('durataMin', 'Durata')
            ->onlyOnDetail()
            ->formatValue(function ($v) {
                if (!is_numeric($v)) return '';
                $h = intdiv((int)$v, 60);
                $m = (int)$v % 60;
                return sprintf('%02d:%02d', $h, $m);
            });

        if ($pageName === Crud::PAGE_INDEX) {
            return [
                AssociationField::new('project')->setLabel('Progetto'),
                AssociationField::new('projectAction')->setLabel('Azione')->autocomplete(),
                DateTimeField::new('startAt')->setLabel('Inizio'),
                DateTimeField::new('endAt')->setLabel('Fine'),
                $durataIndex,
                BooleanField::new('billable')->setLabel('Fatturabile')->renderAsSwitch(false),
            ];
        }

        // NEW/EDIT/DETAIL
        return [
            AssociationField::new('project')->setLabel('Progetto')->autocomplete(),
            AssociationField::new('projectAction')->setLabel('Azione')->autocomplete(),
            DateTimeField::new('startAt')->setLabel('Inizio'),
            DateTimeField::new('endAt')->setLabel('Fine')->hideOnIndex(),
            BooleanField::new('billable')->setLabel('Fatturabile')->renderAsSwitch(false),
            TextareaField::new('descrizione')->setLabel('Descrizione')->hideOnIndex(),

            // in DETTAGLIO mostra la durata calcolata formattata
            $durataDetail,
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $a) => $a->setLabel('Nuovo tempo'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tempo')
            ->setEntityLabelInPlural('Tempi')
            ->setPageTitle(Crud::PAGE_INDEX, 'Tempi registrati')
            ->setDefaultSort(['startAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('project')->setLabel('Progetto'))
            ->add(EntityFilter::new('projectAction')->setLabel('Azione'))
            ->add(DateTimeFilter::new('startAt')->setLabel('Inizio'))
            ->add(DateTimeFilter::new('endAt')->setLabel('Fine'))
            ->add(BooleanFilter::new('billable')->setLabel('Fatturabile'));
    }
}
