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

    public function load(ObjectManager $em): void
    {
        $now = new DateTimeImmutable();

        // -----------------------------------------------------------------------------
        // 1) ACTION STATUS (workflow base)
        // -----------------------------------------------------------------------------
        $stDaIniziare = (new ActionStatus())
            ->setId(Uuid::v7())
            ->setDescrizione('Da iniziare')
            ->setOrdine(1)
            ->setChiusura(false);
        $stInCorso = (new ActionStatus())
            ->setId(Uuid::v7())
            ->setDescrizione('In corso')
            ->setOrdine(2)
            ->setChiusura(false);
        $stCompletato = (new ActionStatus())
            ->setId(Uuid::v7())
            ->setDescrizione('Completato')
            ->setOrdine(3)
            ->setChiusura(true);

        $em->persist($stDaIniziare);
        $em->persist($stInCorso);
        $em->persist($stCompletato);

        // -----------------------------------------------------------------------------
        // 2) ACTION TYPES (categorie attività)
        // -----------------------------------------------------------------------------
        $tSviluppo = (new ActionType())
            ->setId(Uuid::v7())
            ->setDescrizione('Sviluppo')
            ->setColore('#2F6CA3')
            ->setIcona('code')
            ->setFatturabileDefault(true);
        $tRevisione = (new ActionType())
            ->setId(Uuid::v7())
            ->setDescrizione('Revisione')
            ->setColore('#5E5850')
            ->setIcona('review')
            ->setFatturabileDefault(true);
        $tRiunione = (new ActionType())
            ->setId(Uuid::v7())
            ->setDescrizione('Riunione')
            ->setColore('#E0A04B')
            ->setIcona('users')
            ->setFatturabileDefault(false);

        $em->persist($tSviluppo);
        $em->persist($tRevisione);
        $em->persist($tRiunione);

        // -----------------------------------------------------------------------------
        // 3) CLIENTI
        // -----------------------------------------------------------------------------
        $cliA = (new Client())
            ->setId(Uuid::v7())
            ->setDenominazione('Alfa S.r.l.')
            ->setPiva('01234567890')
            ->setCf(null)
            ->setEmail('contatti@alfasrl.it')
            ->setTelefono('+39 011 1234567')
            ->setVia('Via Roma 1')
            ->setCap('10100')
            ->setCitta('Torino')
            ->setNote('Cliente storico; pagamento a 60 giorni.')
            ->setCreatedAt($now)
            ->setUpdatedAt($now);

        $cliB = (new Client())
            ->setId(Uuid::v7())
            ->setDenominazione('Beta Consulting')
            ->setPiva('09876543210')
            ->setCf(null)
            ->setEmail('info@betaconsulting.it')
            ->setTelefono('+39 02 2345678')
            ->setVia('Corso Milano 25')
            ->setCap('20100')
            ->setCitta('Milano')
            ->setNote('Nuovo cliente, priorità alta.')
            ->setCreatedAt($now)
            ->setUpdatedAt($now);

        $em->persist($cliA);
        $em->persist($cliB);

        // -----------------------------------------------------------------------------
        // 4) PROJECT TYPES + TEMPLATE AZIONI
        // -----------------------------------------------------------------------------
        $ptSviluppo = (new ProjectType())
            ->setId(Uuid::v7())
            ->setDescrizione('Sviluppo software')
            ->setCostoOrarioDefault('60.00')
            ->setVersion(1);
        $em->persist($ptSviluppo);

        $ptGrafica = (new ProjectType())
            ->setId(Uuid::v7())
            ->setDescrizione('Grafica')
            ->setCostoOrarioDefault('45.00')
            ->setVersion(1);
        $em->persist($ptGrafica);

        // Template azioni: SVILUPPO
        $tpl1 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptSviluppo)
            ->setTitolo('Analisi requisiti')
            ->setDescrizione('Kickoff e raccolta requisiti iniziali')
            ->setStimaMin(120)
            ->setActionType($tRiunione)
            ->setStatus($stDaIniziare)
            ->setOrdine(1);
        $tpl2 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptSviluppo)
            ->setTitolo('Setup progetto')
            ->setDescrizione('Repo, CI, impalcatura Symfony')
            ->setStimaMin(180)
            ->setActionType($tSviluppo)
            ->setStatus($stDaIniziare)
            ->setOrdine(2);
        $tpl3 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptSviluppo)
            ->setTitolo('Sviluppo MVP')
            ->setDescrizione('Funzionalità core')
            ->setStimaMin(900)
            ->setActionType($tSviluppo)
            ->setStatus($stDaIniziare)
            ->setOrdine(3);
        $tpl4 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptSviluppo)
            ->setTitolo('Revisione UAT')
            ->setDescrizione('Raccolta feedback cliente')
            ->setStimaMin(180)
            ->setActionType($tRevisione)
            ->setStatus($stDaIniziare)
            ->setOrdine(4);

        // Template azioni: GRAFICA
        $tplG1 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptGrafica)
            ->setTitolo('Realizzazione preventivo')
            ->setDescrizione(null)
            ->setStimaMin(60)
            ->setActionType($tRevisione)
            ->setStatus($stDaIniziare)
            ->setOrdine(1);
        $tplG2 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptGrafica)
            ->setTitolo('Definizione obiettivi')
            ->setDescrizione(null)
            ->setStimaMin(150) // 2h30m
            ->setActionType($tRiunione)
            ->setStatus($stDaIniziare)
            ->setOrdine(2);
        $tplG3 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptGrafica)
            ->setTitolo('Bozza grafica')
            ->setDescrizione(null)
            ->setStimaMin(240)
            ->setActionType($tSviluppo)
            ->setStatus($stDaIniziare)
            ->setOrdine(3);
        $tplG4 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptGrafica)
            ->setTitolo('Modifiche bozza')
            ->setDescrizione(null)
            ->setStimaMin(60)
            ->setActionType($tRevisione)
            ->setStatus($stDaIniziare)
            ->setOrdine(4);
        $tplG5 = (new ProjectTypeActionTemplate())
            ->setId(Uuid::v7())
            ->setProjectType($ptGrafica)
            ->setTitolo('Grafica definitiva')
            ->setDescrizione(null)
            ->setStimaMin(120)
            ->setActionType($tSviluppo)
            ->setStatus($stDaIniziare)
            ->setOrdine(5);

        foreach ([$tpl1, $tpl2, $tpl3, $tpl4, $tplG1, $tplG2, $tplG3, $tplG4, $tplG5] as $tpl) {
            $em->persist($tpl);
        }

        // -----------------------------------------------------------------------------
        // 5) USERS
        // -----------------------------------------------------------------------------
        $admin = (new User())
            ->setId(Uuid::v7())
            ->setEmail('admin@tempusfugit.local')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsActive(true);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin')); // cambia subito in prod
        $em->persist($admin);

        $clientUser = (new User())
            ->setId(Uuid::v7())
            ->setEmail('cliente@alfasrl.it')
            ->setRoles(['ROLE_CLIENT'])
            ->setCliente($cliA)
            ->setIsActive(true);
        $clientUser->setPassword($this->hasher->hashPassword($clientUser, 'client'));
        $em->persist($clientUser);

        // -----------------------------------------------------------------------------
        // 6) PROGETTI
        // -----------------------------------------------------------------------------
        $p1 = (new Project())
            ->setId(Uuid::v7())
            ->setClient($cliA)
            ->setType($ptSviluppo)
            ->setTitolo('Intranet gestionale')
            ->setDescrizione('Portale interno per processi aziendali')
            ->setTipologiaFatturazione('A_ORE')
            ->setNote(null)
            ->setDataInizio($now->sub(new DateInterval('P20D')))
            ->setDataFineStimata($now->add(new DateInterval('P40D')))
            ->setDataFineReale(null)
            ->setStato('In corso')
            ->setPathProgetto('/srv/projects/intranet-alfasrl')
            ->setPercentAvanz(0.0)   // sarà aggiornato da logica applicativa
            ->setImportoPreventivo('8500.00')
            ->setCondizioniPagamento('30/60/90')
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
        $em->persist($p1);

        $p2 = (new Project())
            ->setId(Uuid::v7())
            ->setClient($cliB)
            ->setType($ptGrafica)
            ->setTitolo('Branding & Kit social')
            ->setDescrizione('Logo, palette, kit post social')
            ->setTipologiaFatturazione('A_CORPO')
            ->setDataInizio($now->sub(new DateInterval('P5D')))
            ->setDataFineStimata($now->add(new DateInterval('P25D')))
            ->setStato('In corso')
            ->setPathProgetto('/srv/projects/branding-beta')
            ->setPercentAvanz(0.0)
            ->setImportoPreventivo('2400.00')
            ->setCondizioniPagamento('30/60')
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
        $em->persist($p2);

        // -----------------------------------------------------------------------------
        // 7) AZIONI (per p1 creiamo qualche azione già presente)
        // -----------------------------------------------------------------------------
        $a1 = (new Action())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setTitolo('Analisi requisiti')
            ->setDescrizione('Workshop iniziale con reparti')
            ->setStimaMin(120)
            ->setDeadline($now->add(new DateInterval('P3D')))
            ->setType($tRiunione)
            ->setStatus($stInCorso)
            ->setFatturabile(false)
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
        $em->persist($a1);

        $a2 = (new Action())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setTitolo('Setup progetto')
            ->setDescrizione('Repo + CI + impalcatura Symfony')
            ->setStimaMin(180)
            ->setDeadline($now->add(new DateInterval('P5D')))
            ->setType($tSviluppo)
            ->setStatus($stDaIniziare)
            ->setFatturabile(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
        $em->persist($a2);

        $a3 = (new Action())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setTitolo('Sviluppo MVP')
            ->setDescrizione('Auth, CRUD principali, report base')
            ->setStimaMin(900)
            ->setDeadline($now->add(new DateInterval('P25D')))
            ->setType($tSviluppo)
            ->setStatus($stDaIniziare)
            ->setFatturabile(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
        $em->persist($a3);

        // -----------------------------------------------------------------------------
        // 8) TEMPI (timer/registrazioni)
        // -----------------------------------------------------------------------------
        // Tempo su a1 (in corso + uno chiuso)
        $t1 = (new TimeEntry())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setProjectAction($a1) // << property NON "action"
            ->setStartAt($now->sub(new DateInterval('PT2H')))
            ->setEndAt($now->sub(new DateInterval('PT1H30M')))
            ->setDurataMin(30)
            ->setDescrizione('Kickoff call con reparto vendite')
            ->setBillable(false);
        $em->persist($t1);

        // Timer ancora aperto su a1 (endAt null)
        $t2 = (new TimeEntry())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setProjectAction($a1)
            ->setStartAt($now->sub(new DateInterval('PT20M')))
            ->setEndAt(null)
            ->setDurataMin(0) // sarà calcolato alla chiusura
            ->setDescrizione('Allineamento requisiti marketing')
            ->setBillable(false);
        $em->persist($t2);

        // Tempo su a2
        $t3 = (new TimeEntry())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setProjectAction($a2)
            ->setStartAt($now->sub(new DateInterval('P1DT3H')))
            ->setEndAt($now->sub(new DateInterval('P1DT2H')))
            ->setDurataMin(60)
            ->setDescrizione('Impostazione CI + linting')
            ->setBillable(true);
        $em->persist($t3);

        // -----------------------------------------------------------------------------
        // 9) MOVIMENTI (prima nota)
        // -----------------------------------------------------------------------------
        // Debito preventivo creato all’apertura progetto (simuliamo quello che l’event-subscriber farà)
        $mov1 = (new LedgerMovement())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setData($now->sub(new DateInterval('P20D')))
            ->setTipo('DEBITO')
            ->setCategoria('preventivo')
            ->setImporto('8500.00')
            ->setDescrizione('Preventivo progetto Intranet')
            ->setNota(null)
            ->setIvaPercent('22.00')
            ->setPagato(false);
        $em->persist($mov1);

        // Spesa hosting
        $mov2 = (new LedgerMovement())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setData($now->sub(new DateInterval('P10D')))
            ->setTipo('DEBITO')
            ->setCategoria('hosting')
            ->setImporto('120.00')
            ->setDescrizione('Server staging 1 mese')
            ->setIvaPercent('22.00')
            ->setPagato(true);
        $em->persist($mov2);

        // Incasso acconto
        $mov3 = (new LedgerMovement())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setData($now->sub(new DateInterval('P7D')))
            ->setTipo('CREDITO')
            ->setCategoria('acconto')
            ->setImporto('3000.00')
            ->setDescrizione('Acconto da Alfa S.r.l.')
            ->setIvaPercent('22.00')
            ->setPagato(true);
        $em->persist($mov3);

        // -----------------------------------------------------------------------------
        // 10) COMUNICAZIONI
        // -----------------------------------------------------------------------------
        $c1 = (new Communication())
            ->setId(Uuid::v7())
            ->setProject($p1)
            ->setClient($cliA)
            ->setData($now->sub(new DateInterval('P2D')))
            ->setComunicazione('Conferma analisi requisiti e richiesta dashboard KPI');
        $em->persist($c1);

        $c2 = (new Communication())
            ->setId(Uuid::v7())
            ->setProject($p2)
            ->setClient($cliB)
            ->setData($now->sub(new DateInterval('P1D')))
            ->setComunicazione('Invio prima bozza logo e palette');
        $em->persist($c2);

        // -----------------------------------------------------------------------------
        // 11) SETTINGS
        // -----------------------------------------------------------------------------
        $s1 = (new Setting())
            ->setId(Uuid::v7())
            ->setChiave('rounding.minutes')
            ->setValore('15'); // arrotondamento registrazioni tempo a 15 minuti
        $s2 = (new Setting())
            ->setId(Uuid::v7())
            ->setChiave('billing.currency')
            ->setValore('EUR');
        $s3 = (new Setting())
            ->setId(Uuid::v7())
            ->setChiave('projects.base_path')
            ->setValore('/srv/projects');

        $em->persist($s1);
        $em->persist($s2);
        $em->persist($s3);

        // -----------------------------------------------------------------------------
        // FLUSH
        // -----------------------------------------------------------------------------
        $em->flush();
    }
}
