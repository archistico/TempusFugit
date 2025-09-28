<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // scelte tipologia fatturazione e condizioni pagamento
        $tipologie = [
            'A ore'   => 'A_ORE',
            'A corpo' => 'A_CORPO',
        ];

        $condizioniPagamento = [
            '30 gg'        => '30',
            '60 gg'        => '60',
            '30/60'        => '30/60',
            '30/60/90'     => '30/60/90',
            'Personalizzate' => 'PERSONAL',
        ];

        // campo “% avanzamento” SOLO visualizzazione (supponiamo salvato 0..100)
        $percentIndex = NumberField::new('percentAvanz', '%')
            ->setNumDecimals(0)
            ->formatValue(fn ($v) => is_numeric($v) ? round($v).' %' : '');

        if ($pageName === Crud::PAGE_INDEX) {
            return [
                AssociationField::new('client')->setLabel('Cliente'),
                AssociationField::new('type')->setLabel('Tipologia')->autocomplete(),
                TextField::new('titolo'),
                MoneyField::new('importoPreventivo')->setCurrency('EUR')->setLabel('Preventivo'),
                DateField::new('dataInizio')->setLabel('Inizio')->setFormat('dd/MM/yyyy'),
                DateField::new('dataFineStimata')->setLabel('Fine stim.')->setFormat('dd/MM/yyyy'),
                AssociationField::new('stato')->setLabel('Stato')->autocomplete(),
                $percentIndex,
            ];
        }

        // pagine NEW/EDIT/DETAIL
        return [
            AssociationField::new('client')->setLabel('Cliente')->autocomplete(),
            AssociationField::new('type')->setLabel('Tipologia progetto')->autocomplete(),
            TextField::new('titolo'),
            TextareaField::new('descrizione')->hideOnIndex(),

            ChoiceField::new('tipologiaFatturazione')->setChoices($tipologie),
            MoneyField::new('importoPreventivo')->setCurrency('EUR'),

            ChoiceField::new('condizioniPagamento')->setChoices($condizioniPagamento)->hideOnIndex(),

            DateField::new('dataInizio')->setLabel('Data inizio')->setFormat('dd/MM/yyyy'),
            DateField::new('dataFineStimata')->setLabel('Data fine stimata')->setFormat('dd/MM/yyyy')->hideOnIndex(),
            DateField::new('dataFineReale')->setLabel('Data fine reale')->setFormat('dd/MM/yyyy')->hideOnIndex(),

            AssociationField::new('stato')->setLabel('Stato')->autocomplete(),
            TextField::new('pathProgetto')->setLabel('Path su disco')->hideOnIndex(),
            TextareaField::new('note')->onlyOnForms(),

            // Se vuoi mostrare la % anche nel DETAIL (non in edit):
            $percentIndex = NumberField::new('percentAvanz', '%')
                ->setNumDecimals(0)
                ->formatValue(fn ($v) => is_numeric($v) ? round($v).' %' : '')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // NEW esiste già: aggiorno etichetta
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $a) => $a->setLabel('Nuovo progetto'))
            // Aggiungo DETAIL nella lista
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Progetto')
            ->setEntityLabelInPlural('Progetti')
            ->setPageTitle(Crud::PAGE_INDEX, 'Progetti')
            ->setDefaultSort(['dataInizio' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('client')->setLabel('Cliente'))
            ->add(EntityFilter::new('type')->setLabel('Tipologia'))
            ->add(ChoiceFilter::new('tipologiaFatturazione')->setChoices([
                'A ore' => 'A_ORE',
                'A corpo' => 'A_CORPO',
            ]))
            ->add(DateTimeFilter::new('dataInizio')->setLabel('Data inizio'))
            ->add(DateTimeFilter::new('dataFineStimata')->setLabel('Fine stimata'))
            ->add(EntityFilter::new('stato')->setLabel('Stato'))
            ->add(NumericFilter::new('importoPreventivo')->setLabel('Importo'));
    }
}
