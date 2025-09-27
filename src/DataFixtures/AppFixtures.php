<?php

namespace App\DataFixtures;

use App\Entity\Action;
use App\Entity\ActionStatus;
use App\Entity\ActionType;
use App\Entity\Client;
use App\Entity\Communication;
use App\Entity\LedgerMovement;
use App\Entity\Project;
use App\Entity\ProjectType;
use App\Entity\ProjectTypeActionTemplate;
use App\Entity\Setting;
use App\Entity\TimeEntry;
use App\Entity\User;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $om): void
    {
        $now = new DateTimeImmutable();

        // --------------------------------
        // 1) SETTINGS
        // --------------------------------
        $settings = [
            ['chiave' => 'app.name', 'valore' => 'TempusFugit'],
            ['chiave' => 'currency', 'valore' => 'EUR'],
            ['chiave' => 'locale',   'valore' => 'it_IT'],
        ];
        foreach ($settings as $s) {
            $st = new Setting();
            $st->setChiave($s['chiave']);
            $st->setValore($s['valore']);
            $om->persist($st);
        }

        // --------------------------------
        // 2) CLIENTI
        // --------------------------------
        $clients = [];
        $clientData = [
            ['Pippo S.r.l.', 'IT12345678901', 'PLPPLP80A01H501Z', 'pippo@example.com', '055-111111', 'Via Roma 1', '50100', 'Firenze'],
            ['Paperone S.p.A.', 'IT23456789012', 'PPRPRN80A01H501Z', 'paperone@example.com', '02-222222', 'Via Milano 10', '20100', 'Milano'],
            ['Topolinia SNC', 'IT34567890123', 'TPLTPL80A01H501Z', 'topo@example.com', '06-333333', 'Via Lazio 5', '00100', 'Roma'],
            ['Minnie Design', 'IT45678901234', 'MNNMNN80A01H501Z', 'minnie@example.com', '041-444444', 'Calle Lunga 7', '30100', 'Venezia'],
            ['QuiQuoQua', 'IT56789012345', 'QQQQQQ80A01H501Z', 'qqq@example.com', '011-555555', 'Corso Francia 99', '10100', 'Torino'],
        ];
        foreach ($clientData as [$den, $piva, $cf, $email, $tel, $via, $cap, $citta]) {
            $c = new Client();
            $c->setDenominazione($den);
            $c->setPiva($piva);
            $c->setCf($cf);
            $c->setEmail($email);
            $c->setTelefono($tel);
            $c->setVia($via);
            $c->setCap($cap);
            $c->setCitta($citta);
            $c->setNote(null);
            if (method_exists($c, 'setCreatedAt')) { $c->setCreatedAt($now); }
            if (method_exists($c, 'setUpdatedAt')) { $c->setUpdatedAt($now); }
            $om->persist($c);
            $clients[] = $c;
        }

        // --------------------------------
        // 3) USERS
        // --------------------------------
        $admin = new User();
        $admin->setEmail('admin@tempusfugit.local');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $admin->setCliente($clients[0] ?? null);
        $om->persist($admin);

        $user = new User();
        $user->setEmail('user@tempusfugit.local');
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);
        $user->setPassword($this->hasher->hashPassword($user, 'user'));
        $user->setCliente($clients[1] ?? null);
        $om->persist($user);

        // --------------------------------
        // 4) TIPOLOGIE PROGETTO
        // --------------------------------
        $ptSoftware = new ProjectType();
        $ptSoftware->setDescrizione('Sviluppo software');
        // decimali in DB: 10.00 — se memorizzi centesimi come int, metti 1000
        $ptSoftware->setCostoOrarioDefault(10.00);
        if (method_exists($ptSoftware, 'setVersion')) { $ptSoftware->setVersion(1); }
        $om->persist($ptSoftware);

        $ptConsulenza = new ProjectType();
        $ptConsulenza->setDescrizione('Consulenza');
        $ptConsulenza->setCostoOrarioDefault(12.50);
        if (method_exists($ptConsulenza, 'setVersion')) { $ptConsulenza->setVersion(1); }
        $om->persist($ptConsulenza);

        $ptGrafica = new ProjectType();
        $ptGrafica->setDescrizione('Grafica');
        $ptGrafica->setCostoOrarioDefault(9.00);
        if (method_exists($ptGrafica, 'setVersion')) { $ptGrafica->setVersion(1); }
        $om->persist($ptGrafica);

        // --------------------------------
        // 5) STATI AZIONE
        // --------------------------------
        $stDaIniziare = (new ActionStatus())->setDescrizione('Da iniziare')->setChiusura(false)->setOrdine(10);
        $stApprovato  = (new ActionStatus())->setDescrizione('Approvato')->setChiusura(false)->setOrdine(15);
        $stInCorso    = (new ActionStatus())->setDescrizione('In corso')->setChiusura(false)->setOrdine(20);
        $stSospeso    = (new ActionStatus())->setDescrizione('Sospeso')->setChiusura(false)->setOrdine(40);
        $stCompletato = (new ActionStatus())->setDescrizione('Completato')->setChiusura(true)->setOrdine(90);
        foreach ([$stDaIniziare,$stApprovato,$stInCorso,$stSospeso,$stCompletato] as $s) {
            $om->persist($s);
        }

        // --------------------------------
        // 6) TIPI AZIONE
        // --------------------------------
        $tAnalisi   = (new ActionType())->setDescrizione('Analisi')->setFatturabileDefault(true);
        $tSviluppo  = (new ActionType())->setDescrizione('Sviluppo')->setFatturabileDefault(true);
        $tRiunione  = (new ActionType())->setDescrizione('Riunione')->setFatturabileDefault(false);
        $tRevisione = (new ActionType())->setDescrizione('Revisione')->setFatturabileDefault(true);
        foreach ([$tAnalisi,$tSviluppo,$tRiunione,$tRevisione] as $t) { $om->persist($t); }

        $om->flush(); // assicura ID per FK

        // --------------------------------
        // 7) PROGETTI
        // --------------------------------
        $projects = [];
        $progettiSeed = [
            ['Gestionale magazzino', $ptSoftware, $clients[0], 'A_ORE', 3200.00, '30/60', 10],
            ['Sito vetrina',         $ptGrafica,  $clients[1], 'A_CORPO', 1500.00, '30',   20],
            ['Refactoring API',      $ptSoftware, $clients[2], 'A_ORE', 2400.00, '30/60/90', 5],
            ['Formazione team',      $ptConsulenza,$clients[3],'A_ORE',  900.00, '60',   15],
        ];
        foreach ($progettiSeed as [$titolo, $tipo, $cliente, $fatt, $preventivo, $pag, $giorni]) {
            $p = new Project();
            $p->setClient($cliente);
            $p->setType($tipo);
            $p->setTitolo($titolo);
            $p->setDescrizione(null);
            $p->setTipologiaFatturazione($fatt);
            $p->setImportoPreventivo($preventivo);
            $p->setCondizioniPagamento($pag);
            $p->setDataInizio($now->modify('-'.$giorni.' days'));
            $p->setDataFineStimata($now->modify('+'.($giorni+14).' days'));
            $p->setDataFineReale(null);
            // Stato del PROGETTO come relazione a ActionStatus (es. In corso)
            if (method_exists($p, 'setStato')) { $p->setStato($stInCorso); }
            $p->setPathProgetto(null);
            $p->setPercentAvanz(0); // sarà ricalcolato dal tuo subscriber quando crei azioni
            $p->setNote(null);
            if (method_exists($p, 'setCreatedAt')) { $p->setCreatedAt($now); }
            if (method_exists($p, 'setUpdatedAt')) { $p->setUpdatedAt($now); }
            $om->persist($p);
            $projects[] = $p;
        }
        $om->flush();

        // --------------------------------
        // 8) AZIONI per progetto
        // --------------------------------
        $rand = static fn(int $min, int $max) => mt_rand($min,$max);
        foreach ($projects as $p) {
            $bundle = [
                ['Analisi requisiti',  $tAnalisi,   180,  '+3 days',  $stInCorso,    true],
                ['Kickoff con cliente',$tRiunione,   60,  '+1 day',   $stCompletato, false],
                ['Sviluppo Feature A', $tSviluppo,  420,  '+10 days', $stInCorso,    true],
                ['Revisione UX',       $tRevisione, 180,  '+7 days',  $stDaIniziare, true],
            ];
            if ($rand(0,1)) {
                $bundle[] = ['Sviluppo Feature B', $tSviluppo, 360, '+20 days', $stDaIniziare, true];
            }

            foreach ($bundle as [$titolo,$tipo,$stima,$deadlineMod,$stato,$fatt]) {
                $a = new Action();
                $a->setProject($p);
                $a->setType($tipo);
                $a->setStatus($stato);
                $a->setTitolo($titolo);
                $a->setDescrizione(null);
                $a->setStimaMin($stima);
                $a->setDeadline($now->modify($deadlineMod));
                $a->setFatturabile($fatt);
                if (method_exists($a, 'setCreatedAt')) { $a->setCreatedAt($now); }
                if (method_exists($a, 'setUpdatedAt')) { $a->setUpdatedAt($now); }
                $om->persist($a);
            }
        }
        $om->flush();

        // --------------------------------
        // 9) TIME ENTRIES (per alcune azioni)
        // --------------------------------
        $actions = $om->getRepository(Action::class)->findAll();
        foreach ($actions as $a) {
            // 2-4 time logs per azione
            $logs = $rand(2,4);
            for ($i=0; $i<$logs; $i++) {
                $start = $now->modify('-'.($rand(1,15)).' days')->setTime($rand(8,10), [0,15,30,45][$rand(0,3)]);
                $end   = $start->modify('+'.($rand(30,180)).' minutes');

                $t = new TimeEntry();
                $t->setProject($a->getProject());
                // ATTENZIONE: cambia 'projectAction' in 'action' se la tua proprietà si chiama così
                if (method_exists($t, 'setProjectAction')) {
                    $t->setProjectAction($a);
                } elseif (method_exists($t, 'setAction')) {
                    $t->setProjectAction($a);
                }
                $t->setStartAt($start);
                $t->setEndAt($end);
                $t->setBillable($a->isFatturabile() ?? true);
                $t->setDescrizione(null);
                // durataMin verrà calcolata dai lifecycle della entity TimeEntry
                $om->persist($t);
            }
        }
        $om->flush();

        // --------------------------------
        // 10) MOVIMENTI (incassi/spese) per progetto
        // --------------------------------
        foreach ($projects as $p) {
            // spesa
            $m1 = new LedgerMovement();
            $m1->setProject($p);
            $m1->setData($now->modify('-10 days'));
            $m1->setTipo('DEBITO');
            $m1->setImporto( $rand(50, 300) );
            $m1->setDescrizione('Acquisto materiali');
            $om->persist($m1);

            // incasso parziale
            $m2 = new LedgerMovement();
            $m2->setProject($p);
            $m2->setData($now->modify('-3 days'));
            $m2->setTipo('CREDITO');
            $m2->setImporto( $rand(200, 800) );
            $m2->setDescrizione('Acconto cliente');
            $om->persist($m2);
        }
        $om->flush();

        // --------------------------------
        // 11) COMUNICAZIONI
        // --------------------------------
        foreach ($projects as $p) {
            $comm = new Communication();
            $comm->setProject($p);
            $comm->setClient($p->getClient());
            $comm->setData($now->modify('-2 days')->setTime(15, 30));
            $comm->setTipologia('email'); // chiamata | whatsapp | email | sms | altro
            $comm->setComunicazione('Aggiornamento stato progetto e pianificazione prossime attività.');
            $om->persist($comm);
        }
        $om->flush();
    }
}
