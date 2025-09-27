<?php

namespace App\Controller\Admin;

use App\Entity\LedgerMovement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class LedgerMovementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LedgerMovement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('project')->autocomplete(),
            DateField::new('data'),
            ChoiceField::new('tipo')->setChoices(['DEBITO' => 'DEBITO', 'CREDITO' => 'CREDITO']),
            TextField::new('categoria'),
            MoneyField::new('importo')->setCurrency('EUR'),
            TextareaField::new('descrizione')->hideOnIndex(),
            TextareaField::new('nota')->hideOnIndex(),
            TextField::new('ivaPercent')->hideOnIndex()->setLabel('IVA %'),
            BooleanField::new('pagato')->renderAsSwitch(false),
        ];
    }
}
