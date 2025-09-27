<?php

namespace App\Controller\Admin;

use App\Entity\ProjectType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ProjectTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProjectType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // Valori di default
        if ($pageName === Crud::PAGE_NEW) {
            $money = MoneyField::new('costoOrarioDefault', 'Costo orario default')
                ->setCurrency('EUR')
                ->setFormTypeOptions(['data' => 1000.00]);
        
            $version = IntegerField::new('version')->setFormTypeOptions(['data' => 1]);
        
            return [
                TextField::new('descrizione'),
                $money,
                $version->hideOnIndex(),
            ];
        }

        return [
            TextField::new('descrizione')->setLabel('Descrizione'),
            // se salvi in EUR; altrimenti usa NumberField
            MoneyField::new('costoOrarioDefault')->setCurrency('EUR')->setLabel('Costo orario default'),
            IntegerField::new('version')->setLabel('Versione')->hideOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Tipologia Progetto')
                    ->setEntityLabelInPlural('Tipologie Progetto');
    }
}
