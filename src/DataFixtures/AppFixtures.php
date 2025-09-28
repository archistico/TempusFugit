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
            // Case Editrici
            ['Mondadori S.p.A.', 'IT12345678901', 'MNDMND80A01H501Z', 'progetti@mondadori.it', '02-1234567', 'Via Mondadori 1', '20090', 'Segrate (MI)'],
            ['Feltrinelli Editore', 'IT23456789012', 'FLTFLT80A01H501Z', 'digital@feltrinelli.it', '02-2345678', 'Via Andegari 6', '20121', 'Milano'],
            ['Einaudi S.p.A.', 'IT34567890123', 'EINEIN80A01H501Z', 'web@einaudi.it', '011-3456789', 'Via Biancamano 2', '10121', 'Torino'],
            
            // Aziende Tech
            ['TechCorp S.r.l.', 'IT45678901234', 'TCHTCH80A01H501Z', 'info@techcorp.it', '055-4567890', 'Via del Progresso 15', '50100', 'Firenze'],
            ['Digital Solutions S.p.A.', 'IT56789012345', 'DGTDGT80A01H501Z', 'hello@digitalsolutions.it', '06-5678901', 'Via Roma 100', '00100', 'Roma'],
            
            // Privati
            ['Mario Rossi', null, 'RSSMRA80A01H501Z', 'mario.rossi@email.it', '333-1234567', 'Via Garibaldi 10', '20100', 'Milano'],
            ['Giulia Bianchi', null, 'BNCGLI80A01H501Z', 'giulia.bianchi@email.it', '347-2345678', 'Corso Italia 25', '50100', 'Firenze'],
            
            // Aziende tradizionali
            ['Ristorante Da Giuseppe SNC', 'IT67890123456', 'RSTGSP80A01H501Z', 'info@dagiuseppe.it', '041-6789012', 'Calle Larga 12', '30100', 'Venezia'],
            ['Farmacia Centrale S.r.l.', 'IT78901234567', 'FRMFRM80A01H501Z', 'farmacia@centrale.it', '011-7890123', 'Via Po 8', '10100', 'Torino'],
            ['Studio Legale Avv. Verdi', 'IT89012345678', 'VRDVRD80A01H501Z', 'studio@avvverdi.it', '06-8901234', 'Via Nazionale 150', '00100', 'Roma'],
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
        $projectTypes = [];
        
        $ptSoftware = new ProjectType();
        $ptSoftware->setDescrizione('Sviluppo software');
        $ptSoftware->setCostoOrarioDefault(45.00);
        if (method_exists($ptSoftware, 'setVersion')) { $ptSoftware->setVersion(1); }
        $om->persist($ptSoftware);
        $projectTypes[] = $ptSoftware;

        $ptConsulenza = new ProjectType();
        $ptConsulenza->setDescrizione('Consulenza IT');
        $ptConsulenza->setCostoOrarioDefault(50.00);
        if (method_exists($ptConsulenza, 'setVersion')) { $ptConsulenza->setVersion(1); }
        $om->persist($ptConsulenza);
        $projectTypes[] = $ptConsulenza;

        $ptGrafica = new ProjectType();
        $ptGrafica->setDescrizione('Grafica e Design');
        $ptGrafica->setCostoOrarioDefault(35.00);
        if (method_exists($ptGrafica, 'setVersion')) { $ptGrafica->setVersion(1); }
        $om->persist($ptGrafica);
        $projectTypes[] = $ptGrafica;

        $ptWeb = new ProjectType();
        $ptWeb->setDescrizione('Sviluppo Web');
        $ptWeb->setCostoOrarioDefault(40.00);
        if (method_exists($ptWeb, 'setVersion')) { $ptWeb->setVersion(1); }
        $om->persist($ptWeb);
        $projectTypes[] = $ptWeb;

        $ptMobile = new ProjectType();
        $ptMobile->setDescrizione('App Mobile');
        $ptMobile->setCostoOrarioDefault(55.00);
        if (method_exists($ptMobile, 'setVersion')) { $ptMobile->setVersion(1); }
        $om->persist($ptMobile);
        $projectTypes[] = $ptMobile;

        $ptEditoria = new ProjectType();
        $ptEditoria->setDescrizione('Editoria Digitale');
        $ptEditoria->setCostoOrarioDefault(30.00);
        if (method_exists($ptEditoria, 'setVersion')) { $ptEditoria->setVersion(1); }
        $om->persist($ptEditoria);
        $projectTypes[] = $ptEditoria;

        $ptFormazione = new ProjectType();
        $ptFormazione->setDescrizione('Formazione e Training');
        $ptFormazione->setCostoOrarioDefault(60.00);
        if (method_exists($ptFormazione, 'setVersion')) { $ptFormazione->setVersion(1); }
        $om->persist($ptFormazione);
        $projectTypes[] = $ptFormazione;

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
        $actionTypes = [];
        
        // Azioni tecniche
        $tAnalisi   = (new ActionType())->setDescrizione('Analisi')->setFatturabileDefault(true);
        $tSviluppo  = (new ActionType())->setDescrizione('Sviluppo')->setFatturabileDefault(true);
        $tRevisione = (new ActionType())->setDescrizione('Revisione')->setFatturabileDefault(true);
        $tTest      = (new ActionType())->setDescrizione('Test e QA')->setFatturabileDefault(true);
        $tDebug     = (new ActionType())->setDescrizione('Debug e Fix')->setFatturabileDefault(true);
        
        // Azioni creative
        $tDisegno   = (new ActionType())->setDescrizione('Disegno')->setFatturabileDefault(true);
        $tImpaginazione = (new ActionType())->setDescrizione('Impaginazione')->setFatturabileDefault(true);
        $tGrafica   = (new ActionType())->setDescrizione('Grafica')->setFatturabileDefault(true);
        $tUI        = (new ActionType())->setDescrizione('UI/UX Design')->setFatturabileDefault(true);
        
        // Azioni di comunicazione
        $tRiunione  = (new ActionType())->setDescrizione('Riunione')->setFatturabileDefault(false);
        $tPresentazione = (new ActionType())->setDescrizione('Presentazione')->setFatturabileDefault(true);
        $tFormazione = (new ActionType())->setDescrizione('Formazione')->setFatturabileDefault(true);
        
        // Azioni amministrative
        $tDocumentazione = (new ActionType())->setDescrizione('Documentazione')->setFatturabileDefault(true);
        $tRicerca   = (new ActionType())->setDescrizione('Ricerca')->setFatturabileDefault(true);
        $tSetup     = (new ActionType())->setDescrizione('Setup e Configurazione')->setFatturabileDefault(true);
        
        $actionTypes = [$tAnalisi, $tSviluppo, $tRevisione, $tTest, $tDebug, $tDisegno, $tImpaginazione, 
                       $tGrafica, $tUI, $tRiunione, $tPresentazione, $tFormazione, $tDocumentazione, 
                       $tRicerca, $tSetup];
        
        foreach ($actionTypes as $t) { 
            $om->persist($t); 
        }

        $om->flush(); // assicura ID per FK

        // --------------------------------
        // 7) PROGETTI
        // --------------------------------
        $projects = [];
        $progettiSeed = [
            // Progetti Software
            ['Sistema CRM per Mondadori', $ptSoftware, $clients[0], 'A_ORE', 15000.00, '30/60/90', 45, $stInCorso, 25],
            ['App Mobile Feltrinelli', $ptMobile, $clients[1], 'A_CORPO', 25000.00, '50/50', 60, $stInCorso, 15],
            ['Refactoring API Einaudi', $ptSoftware, $clients[2], 'A_ORE', 8000.00, '30/60/90', 30, $stApprovato, 0],
            
            // Progetti Web
            ['Sito E-commerce TechCorp', $ptWeb, $clients[3], 'A_ORE', 12000.00, '30/70', 40, $stInCorso, 40],
            ['Portale Digital Solutions', $ptWeb, $clients[4], 'A_CORPO', 18000.00, '40/60', 50, $stInCorso, 20],
            
            // Progetti Editoria
            ['Piattaforma Editoria Digitale Mondadori', $ptEditoria, $clients[0], 'A_ORE', 20000.00, '30/60/90', 90, $stInCorso, 10],
            ['Sistema di Impaginazione Feltrinelli', $ptEditoria, $clients[1], 'A_ORE', 15000.00, '50/50', 75, $stSospeso, 60],
            
            // Progetti Grafici
            ['Rebranding Ristorante Da Giuseppe', $ptGrafica, $clients[7], 'A_CORPO', 3500.00, '30', 20, $stCompletato, 100],
            ['Identity Farmacia Centrale', $ptGrafica, $clients[8], 'A_CORPO', 2800.00, '30', 15, $stInCorso, 70],
            
            // Progetti Consulenza
            ['Consulenza IT Studio Legale', $ptConsulenza, $clients[9], 'A_ORE', 5000.00, '30', 25, $stInCorso, 30],
            ['Formazione Team TechCorp', $ptFormazione, $clients[3], 'A_ORE', 6000.00, '30', 20, $stCompletato, 100],
            
            // Progetti Privati
            ['Sito Personale Mario Rossi', $ptWeb, $clients[5], 'A_CORPO', 1200.00, '30', 10, $stInCorso, 50],
            ['Portfolio Giulia Bianchi', $ptGrafica, $clients[6], 'A_CORPO', 800.00, '30', 8, $stDaIniziare, 0],
        ];
        
        foreach ($progettiSeed as [$titolo, $tipo, $cliente, $fatt, $preventivo, $pag, $giorni, $stato, $percentuale]) {
            $p = new Project();
            $p->setClient($cliente);
            $p->setType($tipo);
            $p->setTitolo($titolo);
            $p->setDescrizione('Descrizione dettagliata del progetto ' . $titolo);
            $p->setTipologiaFatturazione($fatt);
            $p->setImportoPreventivo($preventivo);
            $p->setCondizioniPagamento($pag);
            $p->setDataInizio($now->modify('-'.$giorni.' days'));
            $p->setDataFineStimata($now->modify('+'.($giorni+14).' days'));
            $p->setDataFineReale($stato === $stCompletato ? $now->modify('-'.mt_rand(1,10).' days') : null);
            $p->setStato($stato);
            $p->setPathProgetto('/progetti/' . strtolower(str_replace(' ', '-', $titolo)));
            $p->setPercentAvanz($percentuale);
            $p->setNote('Note aggiuntive per il progetto ' . $titolo);
            if (method_exists($p, 'setCreatedAt')) { $p->setCreatedAt($now->modify('-'.$giorni.' days')); }
            if (method_exists($p, 'setUpdatedAt')) { $p->setUpdatedAt($now->modify('-'.mt_rand(1,5).' days')); }
            $om->persist($p);
            $projects[] = $p;
        }
        $om->flush();

        // --------------------------------
        // 8) AZIONI per progetto
        // --------------------------------
        $rand = static fn(int $min, int $max) => mt_rand($min,$max);
        
        // Template di azioni per tipo di progetto
        $actionTemplates = [
            'Sviluppo software' => [
                ['Analisi requisiti', $tAnalisi, 240, '+2 days', $stCompletato, true],
                ['Kickoff tecnico', $tRiunione, 90, '+1 day', $stCompletato, false],
                ['Setup ambiente', $tSetup, 120, '+1 day', $stCompletato, true],
                ['Sviluppo core', $tSviluppo, 600, '+15 days', $stInCorso, true],
                ['Test unitari', $tTest, 180, '+12 days', $stDaIniziare, true],
                ['Documentazione API', $tDocumentazione, 120, '+18 days', $stDaIniziare, true],
                ['Debug e fix', $tDebug, 240, '+20 days', $stDaIniziare, true],
            ],
            'App Mobile' => [
                ['Analisi UX', $tUI, 180, '+3 days', $stCompletato, true],
                ['Prototipo', $tDisegno, 300, '+5 days', $stInCorso, true],
                ['Sviluppo iOS', $tSviluppo, 480, '+20 days', $stInCorso, true],
                ['Sviluppo Android', $tSviluppo, 480, '+25 days', $stDaIniziare, true],
                ['Test su dispositivi', $tTest, 240, '+30 days', $stDaIniziare, true],
                ['Pubblicazione store', $tSetup, 120, '+35 days', $stDaIniziare, true],
            ],
            'Sviluppo Web' => [
                ['Analisi funzionale', $tAnalisi, 180, '+2 days', $stCompletato, true],
                ['Design UI/UX', $tUI, 360, '+5 days', $stInCorso, true],
                ['Sviluppo frontend', $tSviluppo, 480, '+15 days', $stInCorso, true],
                ['Sviluppo backend', $tSviluppo, 360, '+12 days', $stDaIniziare, true],
                ['Integrazione', $tSviluppo, 180, '+18 days', $stDaIniziare, true],
                ['Test e ottimizzazione', $tTest, 120, '+20 days', $stDaIniziare, true],
            ],
            'Editoria Digitale' => [
                ['Analisi contenuti', $tAnalisi, 120, '+2 days', $stCompletato, true],
                ['Progettazione layout', $tUI, 240, '+5 days', $stInCorso, true],
                ['Impaginazione', $tImpaginazione, 360, '+10 days', $stInCorso, true],
                ['Sviluppo reader', $tSviluppo, 480, '+15 days', $stDaIniziare, true],
                ['Test compatibilità', $tTest, 180, '+18 days', $stDaIniziare, true],
                ['Pubblicazione', $tSetup, 90, '+20 days', $stDaIniziare, true],
            ],
            'Grafica e Design' => [
                ['Brief creativo', $tRiunione, 60, '+1 day', $stCompletato, false],
                ['Concept design', $tDisegno, 240, '+3 days', $stInCorso, true],
                ['Sviluppo grafica', $tGrafica, 360, '+7 days', $stInCorso, true],
                ['Impaginazione', $tImpaginazione, 180, '+10 days', $stDaIniziare, true],
                ['Revisione cliente', $tRevisione, 60, '+12 days', $stDaIniziare, true],
                ['Finalizzazione', $tGrafica, 120, '+14 days', $stDaIniziare, true],
            ],
            'Consulenza IT' => [
                ['Audit sistema', $tAnalisi, 300, '+3 days', $stCompletato, true],
                ['Presentazione risultati', $tPresentazione, 90, '+5 days', $stInCorso, true],
                ['Piano di miglioramento', $tDocumentazione, 180, '+7 days', $stInCorso, true],
                ['Implementazione', $tSetup, 240, '+10 days', $stDaIniziare, true],
                ['Formazione utenti', $tFormazione, 120, '+12 days', $stDaIniziare, true],
            ],
            'Formazione e Training' => [
                ['Analisi esigenze', $tAnalisi, 120, '+2 days', $stCompletato, true],
                ['Progettazione corso', $tDocumentazione, 180, '+5 days', $stInCorso, true],
                ['Preparazione materiali', $tDocumentazione, 240, '+8 days', $stInCorso, true],
                ['Erogazione formazione', $tFormazione, 480, '+10 days', $stDaIniziare, true],
                ['Valutazione apprendimento', $tTest, 90, '+12 days', $stDaIniziare, true],
            ],
        ];
        
        foreach ($projects as $p) {
            $projectType = $p->getType()->getDescrizione();
            $template = $actionTemplates[$projectType] ?? $actionTemplates['Sviluppo software'];
            
            // Aggiungi 1-3 azioni extra casuali
            $extraActions = [
                ['Ricerca tecnologie', $tRicerca, 120, '+25 days', $stDaIniziare, true],
                ['Riunione di aggiornamento', $tRiunione, 60, '+15 days', $stDaIniziare, false],
                ['Revisione codice', $tRevisione, 180, '+22 days', $stDaIniziare, true],
            ];
            
            $bundle = array_merge($template, array_slice($extraActions, 0, $rand(1, 3)));

            foreach ($bundle as [$titolo, $tipo, $stima, $deadlineMod, $stato, $fatt]) {
                $a = new Action();
                $a->setProject($p);
                $a->setType($tipo);
                $a->setStatus($stato);
                $a->setTitolo($titolo);
                $a->setDescrizione('Descrizione dettagliata per: ' . $titolo);
                $a->setStimaMin($stima);
                $a->setDeadline($now->modify($deadlineMod));
                $a->setFatturabile($fatt);
                if (method_exists($a, 'setCreatedAt')) { $a->setCreatedAt($now->modify('-'.$rand(1,10).' days')); }
                if (method_exists($a, 'setUpdatedAt')) { $a->setUpdatedAt($now->modify('-'.$rand(1,3).' days')); }
                $om->persist($a);
            }
        }
        $om->flush();

        // --------------------------------
        // 9) TIME ENTRIES (per alcune azioni)
        // --------------------------------
        $actions = $om->getRepository(Action::class)->findAll();
        $timeEntries = [];
        
        foreach ($actions as $a) {
            // Solo per azioni in corso o completate
            if ($a->getStatus() && $a->getStatus()->isChiusura() === false) {
                $logs = $rand(1, 3);
            } elseif ($a->getStatus() && $a->getStatus()->isChiusura() === true) {
                $logs = $rand(3, 6); // Azioni completate hanno più time entries
            } else {
                $logs = $rand(0, 2); // Azioni da iniziare hanno meno time entries
            }
            
            for ($i=0; $i<$logs; $i++) {
                $daysAgo = $rand(1, 30);
                $startHour = $rand(8, 17);
                $startMinute = [0, 15, 30, 45][$rand(0, 3)];
                $duration = $rand(30, 240); // 30 minuti a 4 ore
                
                $start = $now->modify('-'.$daysAgo.' days')
                           ->setTime($startHour, $startMinute);
                $end = $start->modify('+'.$duration.' minutes');

                $t = new TimeEntry();
                $t->setProject($a->getProject());
                if (method_exists($t, 'setProjectAction')) {
                    $t->setProjectAction($a);
                } elseif (method_exists($t, 'setAction')) {
                    $t->setProjectAction($a);
                }
                $t->setStartAt($start);
                $t->setEndAt($end);
                $t->setBillable($a->isFatturabile() ?? true);
                $t->setDescrizione('Lavoro su: ' . $a->getTitolo());
                $om->persist($t);
                $timeEntries[] = $t;
            }
        }
        $om->flush();

        // --------------------------------
        // 10) MOVIMENTI (incassi/spese) per progetto
        // --------------------------------
        $movementTypes = [
            'DEBITO' => [
                'Acquisto software', 'Licenze annuali', 'Hardware', 'Servizi cloud', 
                'Materiale di consumo', 'Corso di formazione', 'Viaggio cliente',
                'Hosting e domini', 'Strumenti di sviluppo', 'Marketing digitale'
            ],
            'CREDITO' => [
                'Acconto progetto', 'Pagamento fattura', 'Saldo finale', 'Bonus qualità',
                'Pagamento anticipato', 'Rimborso spese', 'Commissione vendita',
                'Pagamento milestone', 'Fattura mensile', 'Pagamento straordinario'
            ]
        ];
        
        foreach ($projects as $p) {
            $projectValue = $p->getImportoPreventivo();
            $projectStart = $p->getDataInizio();
            $projectEnd = $p->getDataFineStimata();
            
            // Genera 2-5 movimenti per progetto
            $numMovements = $rand(2, 5);
            
            for ($i = 0; $i < $numMovements; $i++) {
                $tipo = $rand(0, 1) ? 'DEBITO' : 'CREDITO';
                $descrizioni = $movementTypes[$tipo];
                $descrizione = $descrizioni[$rand(0, count($descrizioni) - 1)];
                
                // Calcola importo basato sul valore del progetto
                if ($tipo === 'CREDITO') {
                    $importo = $projectValue * ($rand(10, 40) / 100); // 10-40% del progetto
                } else {
                    $importo = $projectValue * ($rand(2, 15) / 100); // 2-15% del progetto
                }
                
                // Data casuale tra inizio e fine progetto
                $daysDiff = $projectEnd->diff($projectStart)->days;
                $randomDays = $rand(0, $daysDiff);
                $data = $projectStart->modify('+'.$randomDays.' days');
                
                $m = new LedgerMovement();
                $m->setProject($p);
                $m->setData($data);
                $m->setTipo($tipo);
                $m->setImporto(round($importo, 2));
                $m->setDescrizione($descrizione);
                $om->persist($m);
            }
        }
        $om->flush();

        // --------------------------------
        // 11) COMUNICAZIONI
        // --------------------------------
        $communicationTypes = ['email', 'chiamata', 'whatsapp', 'sms', 'riunione', 'videoconferenza'];
        $communicationTemplates = [
            'Aggiornamento stato progetto e pianificazione prossime attività.',
            'Discussione sui requisiti e specifiche tecniche.',
            'Presentazione del prototipo e raccolta feedback.',
            'Risoluzione di problematiche emerse durante lo sviluppo.',
            'Pianificazione delle prossime milestone e scadenze.',
            'Conferma approvazione per la fase successiva.',
            'Richiesta di modifiche e miglioramenti.',
            'Coordinamento per i test e la fase di validazione.',
            'Preparazione per la consegna finale del progetto.',
            'Follow-up post consegna e supporto tecnico.',
        ];
        
        foreach ($projects as $p) {
            // 2-4 comunicazioni per progetto
            $numComms = $rand(2, 4);
            
            for ($i = 0; $i < $numComms; $i++) {
                $comm = new Communication();
                $comm->setProject($p);
                $comm->setClient($p->getClient());
                
                // Data casuale negli ultimi 30 giorni
                $daysAgo = $rand(1, 30);
                $hour = $rand(9, 17);
                $minute = [0, 15, 30, 45][$rand(0, 3)];
                $comm->setData($now->modify('-'.$daysAgo.' days')->setTime($hour, $minute));
                
                $comm->setTipologia($communicationTypes[$rand(0, count($communicationTypes) - 1)]);
                $comm->setComunicazione($communicationTemplates[$rand(0, count($communicationTemplates) - 1)]);
                $om->persist($comm);
            }
        }
        $om->flush();
    }
}
