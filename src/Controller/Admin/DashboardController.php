<?php

namespace App\Controller\Admin;

use App\Entity\Action;
use App\Entity\ActionStatus;
use App\Entity\ActionType;
use App\Entity\Client;
use App\Entity\Communication;
use App\Entity\LedgerMovement;
use App\Entity\Project;
use App\Entity\ProjectType;
use App\Entity\Setting;
use App\Entity\TimeEntry;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tempusfugit');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Anagrafica');
        yield MenuItem::linkToCrud('Clienti', 'fa fa-building', Client::class);
        yield MenuItem::linkToCrud('Tipologie Progetto', 'fa fa-layer-group', ProjectType::class);
        yield MenuItem::linkToCrud('Tipologie Azioni', 'fa fa-tags', ActionType::class);
        yield MenuItem::linkToCrud('Tipologie Stati', 'fa fa-flag', ActionStatus::class);

        yield MenuItem::section('Operativo');
        yield MenuItem::linkToCrud('Progetti', 'fa fa-diagram-project', Project::class);
        yield MenuItem::linkToCrud('Azioni', 'fa fa-list-check', Action::class);
        yield MenuItem::linkToCrud('Tempi', 'fa fa-stopwatch', TimeEntry::class);
        yield MenuItem::linkToCrud('Movimenti', 'fa fa-receipt', LedgerMovement::class);
        yield MenuItem::linkToCrud('Comunicazioni', 'fa fa-comments', Communication::class);

        yield MenuItem::section('Impostazioni');
        yield MenuItem::linkToCrud('Settings', 'fa fa-gear', Setting::class);
        yield MenuItem::linkToCrud('Utenti', 'fa fa-user', User::class);
    }
}
