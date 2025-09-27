<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('denominazione')->setLabel('Denominazione'),
            TextField::new('piva')->setLabel('P. IVA')->hideOnIndex(),
            TextField::new('cf')->setLabel('C.F.')->hideOnIndex(),
            TextField::new('email')->setLabel('Email'),
            TextField::new('telefono')->setLabel('Telefono'),
            TextField::new('via')->setLabel('Indirizzo')->hideOnIndex(),
            TextField::new('cap')->setLabel('CAP')->hideOnIndex(),
            TextField::new('citta')->setLabel('Città')->hideOnIndex(),
            TextareaField::new('note')->hideOnIndex(),

            // se hai createdAt/updatedAt nelle entity:
            DateTimeField::new('createdAt')->hideOnForm()->onlyOnDetail(),
            DateTimeField::new('updatedAt')->hideOnForm()->onlyOnDetail(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // NEW esiste già su INDEX: lo aggiorno soltanto
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Nuovo cliente')->setIcon('fa fa-plus');
            })
            // DETAIL non è presente di default su INDEX: lo aggiungo
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // (facoltativo) personalizza EDIT/DELETE, o limita i permessi:
            // ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // niente override CSRF qui: resta attivo di default
        return $crud->setEntityLabelInSingular('Cliente')
                    ->setEntityLabelInPlural('Clienti')
                    ->setPageTitle(Crud::PAGE_INDEX, 'Clienti');
    }
}
