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
        $now = new \DateTimeImmutable();
        $dayStart   = $now->setTime(0,0);
        $weekStart  = $now->modify(('1' === $now->format('N')) ? 'today' : 'last monday')->setTime(0,0);
        $monthStart = $now->modify('first day of this month')->setTime(0,0);
        $yearStart  = $now->modify('first day of January')->setTime(0,0);
        $lastMonthStart = $now->modify('first day of last month')->setTime(0,0);
        $lastMonthEnd = $now->modify('last day of last month')->setTime(23,59,59);

        // --- STATISTICHE TEMPO ---
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
        $minsLastMonth = $minutes($lastMonthStart, $lastMonthEnd);
        $minsYear   = $minutes($yearStart, $now);

        // --- EFFICIENZA E FATTURAZIONE ---
        $qTot = $this->em->createQueryBuilder()
            ->select('COALESCE(SUM(t.durataMin),0)')->from(TimeEntry::class,'t')
            ->andWhere('t.startAt >= :from')->andWhere('t.startAt <= :to')
            ->setParameter('from',$monthStart)->setParameter('to',$now);
        $qBill = clone $qTot; $qBill->andWhere('t.billable = 1');
        $totMinMonth   = (int)$qTot->getQuery()->getSingleScalarResult();
        $billMinMonth  = (int)$qBill->getQuery()->getSingleScalarResult();
        $efficiencyPct = $totMinMonth > 0 ? round($billMinMonth / $totMinMonth * 100) : 0;

        // Efficienza mese precedente per confronto
        $qTotLast = $this->em->createQueryBuilder()
            ->select('COALESCE(SUM(t.durataMin),0)')->from(TimeEntry::class,'t')
            ->andWhere('t.startAt >= :from')->andWhere('t.startAt <= :to')
            ->setParameter('from',$lastMonthStart)->setParameter('to',$lastMonthEnd);
        $qBillLast = clone $qTotLast; $qBillLast->andWhere('t.billable = 1');
        $totMinLastMonth = (int)$qTotLast->getQuery()->getSingleScalarResult();
        $billMinLastMonth = (int)$qBillLast->getQuery()->getSingleScalarResult();
        $efficiencyLastMonth = $totMinLastMonth > 0 ? round($billMinLastMonth / $totMinLastMonth * 100) : 0;

        // --- STATISTICHE FINANZIARIE ---
        $sumMov = function(string $tipo, \DateTimeImmutable $from, \DateTimeImmutable $to): float {
            return (float) $this->em->createQueryBuilder()
                ->select('COALESCE(SUM(m.importo),0)')
                ->from(LedgerMovement::class,'m')
                ->andWhere('m.tipo = :tipo')->setParameter('tipo',$tipo)
                ->andWhere('m.data >= :from')->andWhere('m.data <= :to')
                ->setParameter('from', $from)->setParameter('to', $to)
                ->getQuery()->getSingleScalarResult();
        };

        $entrateMonth = $sumMov('CREDITO', $monthStart, $now);
        $speseMonth   = $sumMov('DEBITO', $monthStart, $now);
        $entrateLastMonth = $sumMov('CREDITO', $lastMonthStart, $lastMonthEnd);
        $speseLastMonth = $sumMov('DEBITO', $lastMonthStart, $lastMonthEnd);
        $entrateYear = $sumMov('CREDITO', $yearStart, $now);
        $speseYear = $sumMov('DEBITO', $yearStart, $now);

        $utileMese = $entrateMonth - $speseMonth;
        $utileLastMonth = $entrateLastMonth - $speseLastMonth;
        $utileAnno = $entrateYear - $speseYear;

        // --- STATISTICHE PROGETTI ---
        $totalProjects = $this->em->getRepository(Project::class)->count([]);
        $activeProjects = $this->em->getRepository(Project::class)->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.stato != :done OR p.stato IS NULL')
            ->setParameter('done', 'Completato')
            ->getQuery()->getSingleScalarResult();
        $completedProjects = $totalProjects - $activeProjects;
        $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0;

        // Progetti in ritardo
        $overdueProjects = $this->em->getRepository(Project::class)->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.dataFineStimata < :now')
            ->andWhere('p.stato != :done OR p.stato IS NULL')
            ->setParameter('now', $now)
            ->setParameter('done', 'Completato')
            ->getQuery()->getSingleScalarResult();

        // --- STATISTICHE AZIONI ---
        $totalActions = $this->em->getRepository(Action::class)->count([]);
        $completedActions = $this->em->getRepository(Action::class)->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->leftJoin('a.status', 's')
            ->andWhere('s.chiusura = 1')
            ->getQuery()->getSingleScalarResult();
        $pendingActions = $totalActions - $completedActions;
        $actionCompletionRate = $totalActions > 0 ? round(($completedActions / $totalActions) * 100) : 0;

        // Azioni in ritardo
        $overdueActions = $this->em->getRepository(Action::class)->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->leftJoin('a.status', 's')
            ->andWhere('a.deadline < :now')
            ->andWhere('s.chiusura = 0 OR s.id IS NULL')
            ->setParameter('now', $now)
            ->getQuery()->getSingleScalarResult();

        // --- STATISTICHE CLIENTI ---
        $totalClients = $this->em->getRepository(Client::class)->count([]);
        $activeClients = $this->em->getRepository(Client::class)->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.id)')
            ->leftJoin('c.projects', 'p')
            ->andWhere('p.stato != :done OR p.stato IS NULL')
            ->setParameter('done', 'Completato')
            ->getQuery()->getSingleScalarResult();

        // --- MEDIA ORE PER GIORNO ---
        $workingDaysThisMonth = $this->getWorkingDaysInMonth($monthStart, $now);
        $avgHoursPerDay = $workingDaysThisMonth > 0 ? round(($minsMonth / 60) / $workingDaysThisMonth, 1) : 0;

        // --- LISTE PER DASHBOARD ---
        $projects = $this->em->getRepository(Project::class)->createQueryBuilder('p')
            ->andWhere('p.stato != :done OR p.stato IS NULL')->setParameter('done', 'Completato')
            ->orderBy('p.dataFineStimata', 'ASC')->addOrderBy('p.dataInizio', 'DESC')
            ->setMaxResults(8)->getQuery()->getResult();

        $actions = $this->em->getRepository(Action::class)->createQueryBuilder('a')
            ->leftJoin('a.status', 's')->addSelect('s')
            ->andWhere('s.chiusura = 0 OR s.id IS NULL')
            ->orderBy('a.deadline', 'ASC')->addOrderBy('a.createdAt', 'DESC')
            ->setMaxResults(8)->getQuery()->getResult();

        $timeEntries = $this->em->getRepository(TimeEntry::class)->createQueryBuilder('t')
            ->orderBy('t.startAt', 'DESC')->setMaxResults(8)->getQuery()->getResult();

        $movements = $this->em->getRepository(LedgerMovement::class)->createQueryBuilder('m')
            ->orderBy('m.data', 'DESC')->setMaxResults(8)->getQuery()->getResult();

        return $this->render('admin/dashboard.html.twig', [
            // Liste
            'projects'       => $projects,
            'actions'        => $actions,
            'timeEntries'    => $timeEntries,
            'movements'      => $movements,
            
            // Tempo
            'minsToday'      => $minsToday,
            'minsWeek'       => $minsWeek,
            'minsMonth'      => $minsMonth,
            'minsLastMonth'  => $minsLastMonth,
            'minsYear'       => $minsYear,
            'avgHoursPerDay' => $avgHoursPerDay,
            
            // Efficienza
            'efficiencyPct'  => $efficiencyPct,
            'efficiencyLastMonth' => $efficiencyLastMonth,
            'efficiencyTrend' => $efficiencyPct - $efficiencyLastMonth,
            
            // Finanze
            'entrateMonth'   => $entrateMonth,
            'speseMonth'     => $speseMonth,
            'utileMese'      => $utileMese,
            'entrateLastMonth' => $entrateLastMonth,
            'speseLastMonth' => $speseLastMonth,
            'utileLastMonth' => $utileLastMonth,
            'utileAnno'      => $utileAnno,
            'revenueTrend'   => $entrateMonth - $entrateLastMonth,
            'expenseTrend'   => $speseMonth - $speseLastMonth,
            
            // Progetti
            'totalProjects'  => $totalProjects,
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects,
            'completionRate' => $completionRate,
            'overdueProjects' => $overdueProjects,
            
            // Azioni
            'totalActions'   => $totalActions,
            'completedActions' => $completedActions,
            'pendingActions' => $pendingActions,
            'actionCompletionRate' => $actionCompletionRate,
            'overdueActions' => $overdueActions,
            
            // Clienti
            'totalClients'   => $totalClients,
            'activeClients'  => $activeClients,
        ]);
    }

    private function getWorkingDaysInMonth(\DateTimeImmutable $start, \DateTimeImmutable $end): int
    {
        $workingDays = 0;
        $current = $start;
        
        while ($current <= $end) {
            // Esclude sabato (6) e domenica (7)
            if ($current->format('N') < 6) {
                $workingDays++;
            }
            $current = $current->modify('+1 day');
        }
        
        return $workingDays;
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
