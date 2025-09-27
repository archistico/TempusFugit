<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProjectType $type = null;

    #[ORM\Column(length: 255)]
    private ?string $titolo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descrizione = null;

    #[ORM\Column(length: 255)]
    private ?string $tipologiaFatturazione = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dataInizio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dataFineStimata = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dataFineReale = null;

    #[ORM\Column(length: 255)]
    private ?string $stato = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pathProgetto = null;

    #[ORM\Column]
    private ?float $percentAvanz = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $importoPreventivo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $condizioniPagamento = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getType(): ?ProjectType
    {
        return $this->type;
    }

    public function setType(?ProjectType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTitolo(): ?string
    {
        return $this->titolo;
    }

    public function setTitolo(string $titolo): static
    {
        $this->titolo = $titolo;

        return $this;
    }

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }

    public function setDescrizione(?string $descrizione): static
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    public function getTipologiaFatturazione(): ?string
    {
        return $this->tipologiaFatturazione;
    }

    public function setTipologiaFatturazione(string $tipologiaFatturazione): static
    {
        $this->tipologiaFatturazione = $tipologiaFatturazione;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDataInizio(): ?\DateTime
    {
        return $this->dataInizio;
    }

    public function setDataInizio(\DateTime $dataInizio): static
    {
        $this->dataInizio = $dataInizio;

        return $this;
    }

    public function getDataFineStimata(): ?\DateTime
    {
        return $this->dataFineStimata;
    }

    public function setDataFineStimata(?\DateTime $dataFineStimata): static
    {
        $this->dataFineStimata = $dataFineStimata;

        return $this;
    }

    public function getDataFineReale(): ?\DateTime
    {
        return $this->dataFineReale;
    }

    public function setDataFineReale(?\DateTime $dataFineReale): static
    {
        $this->dataFineReale = $dataFineReale;

        return $this;
    }

    public function getStato(): ?string
    {
        return $this->stato;
    }

    public function setStato(string $stato): static
    {
        $this->stato = $stato;

        return $this;
    }

    public function getPathProgetto(): ?string
    {
        return $this->pathProgetto;
    }

    public function setPathProgetto(?string $pathProgetto): static
    {
        $this->pathProgetto = $pathProgetto;

        return $this;
    }

    public function getPercentAvanz(): ?float
    {
        return $this->percentAvanz;
    }

    public function setPercentAvanz(float $percentAvanz): static
    {
        $this->percentAvanz = $percentAvanz;

        return $this;
    }

    public function getImportoPreventivo(): ?string
    {
        return $this->importoPreventivo;
    }

    public function setImportoPreventivo(string $importoPreventivo): static
    {
        $this->importoPreventivo = $importoPreventivo;

        return $this;
    }

    public function getCondizioniPagamento(): ?string
    {
        return $this->condizioniPagamento;
    }

    public function setCondizioniPagamento(?string $condizioniPagamento): static
    {
        $this->condizioniPagamento = $condizioniPagamento;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
