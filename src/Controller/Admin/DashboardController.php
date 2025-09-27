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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // --- LISTE ---

        // Progetti non conclusi (stato != 'Completato'), in scadenza per primi
        $projects = $this->em->getRepository(Project::class)->createQueryBuilder('p')
            ->andWhere('p.stato != :done OR p.stato IS NULL')->setParameter('done', 'Completato')
            ->orderBy('p.dataFineStimata', 'ASC')->addOrderBy('p.dataInizio', 'DESC')
            ->setMaxResults(8)->getQuery()->getResult();

        // Azioni non terminate (status.chiusura = 0), scadenza imminente per prime
        $actions = $this->em->getRepository(Action::class)->createQueryBuilder('a')
            ->leftJoin('a.status', 's')->addSelect('s')
            ->andWhere('s.chiusura = 0 OR s.id IS NULL')
            ->orderBy('a.deadline', 'ASC')->addOrderBy('a.createdAt', 'DESC')
            ->setMaxResults(8)->getQuery()->getResult();

        // Ultimi tempi registrati
        $timeEntries = $this->em->getRepository(TimeEntry::class)->createQueryBuilder('t')
            ->orderBy('t.startAt', 'DESC')->setMaxResults(8)->getQuery()->getResult();

        // Ultimi movimenti (incassi/spese)
        $movements = $this->em->getRepository(LedgerMovement::class)->createQueryBuilder('m')
            ->orderBy('m.data', 'DESC')->setMaxResults(8)->getQuery()->getResult();

        // --- KPI / STATISTICHE SEMPLICI ---

        $now = new \DateTimeImmutable();
        $dayStart   = $now->setTime(0,0);
        $weekStart  = $now->modify(('1' === $now->format('N')) ? 'today' : 'last monday')->setTime(0,0);
        $monthStart = $now->modify('first day of this month')->setTime(0,0);

        $minutes = fn(\DateTimeImmutable $from,\DateTimeImmutable $to) => (int) (
            $this->em->createQueryBuilder()
                ->select('COALESCE(SUM(t.durataMin),0)')
                ->from(TimeEntry::class,'t')
                ->andWhere('t.startAt >= :f')->andWhere('(t.endAt IS NULL OR t.endAt <= :t)')
                ->setParameter('f',$from)->setParameter('t',$to)
                ->getQuery()->getSingleScalarResult()
        );

        $minsToday  = $minutes($dayStart, $now);
        $minsWeek   = $minutes($weekStart, $now);
        $minsMonth  = $minutes($monthStart, $now);

        // Efficienza (fatturabili / totali) nel mese
        $qTot = $this->em->createQueryBuilder()
            ->select('COALESCE(SUM(t.durataMin),0)')->from(TimeEntry::class,'t')
            ->andWhere('t.startAt >= :from')->andWhere('t.startAt <= :to')
            ->setParameter('from',$monthStart)->setParameter('to',$now);
        $qBill = clone $qTot; $qBill->andWhere('t.billable = 1');
        $totMinMonth   = (int)$qTot->getQuery()->getSingleScalarResult();
        $billMinMonth  = (int)$qBill->getQuery()->getSingleScalarResult();
        $efficiencyPct = $totMinMonth > 0 ? round($billMinMonth / $totMinMonth * 100) : 0;

        // Entrate vs Spese nel mese
        $sumMov = function(string $tipo): float {
            return (float) $this->em->createQueryBuilder()
                ->select('COALESCE(SUM(m.importo),0)')
                ->from(LedgerMovement::class,'m')
                ->andWhere('m.tipo = :tipo')->setParameter('tipo',$tipo)
                ->andWhere('m.data >= :from')->andWhere('m.data <= :to')
                ->setParameter('from', (new \DateTimeImmutable('first day of this month'))->setTime(0,0))
                ->setParameter('to', new \DateTimeImmutable())
                ->getQuery()->getSingleScalarResult();
        };
        $entrateMonth = $sumMov('CREDITO');
        $speseMonth   = $sumMov('DEBITO');

        return $this->render('admin/dashboard.html.twig', [
            'projects'       => $projects,
            'actions'        => $actions,
            'timeEntries'    => $timeEntries,
            'movements'      => $movements,
            'minsToday'      => $minsToday,
            'minsWeek'       => $minsWeek,
            'minsMonth'      => $minsMonth,
            'efficiencyPct'  => $efficiencyPct,
            'entrateMonth'   => $entrateMonth,
            'speseMonth'     => $speseMonth,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            ->setTitle('Tempus Fugit')

            // by default EasyAdmin displays a black square as its default favicon;
            // use this method to display a custom favicon: the given path is passed
            // "as is" to the Twig asset() function:
            // <link rel="shortcut icon" href="{{ asset('...') }}">
            ->setFaviconPath('favicon.svg')
            ->renderContentMaximized()
            ->setDefaultColorScheme('dark')
        ;
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
