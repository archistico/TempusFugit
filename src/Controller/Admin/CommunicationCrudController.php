<?php

namespace App\Controller\Admin;

use App\Entity\Communication;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class CommunicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Communication::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $tipologiaChoices = [
            'Chiamata' => 'chiamata',
            'WhatsApp' => 'whatsapp',
            'Email'    => 'email',
            'SMS'      => 'sms',
            'Altro'    => 'altro',
        ];

        return [
            AssociationField::new('project')->setLabel('Progetto')->autocomplete(),
            AssociationField::new('client')->setLabel('Cliente')->autocomplete(),
            DateTimeField::new('data')->setLabel('Data/Ora')->setFormat('dd/MM/yyyy HH:mm'),
            ChoiceField::new('tipologia')->setLabel('Tipologia')->setChoices($tipologiaChoices),
            TextareaField::new('comunicazione')->setLabel('Comunicazione')->renderAsHtml(false),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Comunicazione')
            ->setEntityLabelInPlural('Comunicazioni')
            ->setPageTitle(Crud::PAGE_INDEX, 'Comunicazioni')
            ->setDefaultSort(['data' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $a) => $a->setLabel('Nuova comunicazione'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('project')->setLabel('Progetto'))
            ->add(EntityFilter::new('client')->setLabel('Cliente'))
            ->add(DateTimeFilter::new('data')->setLabel('Data'))
            ->add(ChoiceFilter::new('tipologia')->setLabel('Tipologia')->setChoices([
                'Chiamata' => 'chiamata',
                'WhatsApp' => 'whatsapp',
                'Email'    => 'email',
                'SMS'      => 'sms',
                'Altro'    => 'altro',
            ]));
    }
}
