<?php

namespace App\Entity;

use App\Repository\LedgerMovementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: LedgerMovementRepository::class)]
class LedgerMovement
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'ledgerMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $data = null;

    #[ORM\Column(length: 255)]
    private ?string $tipo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $importo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descrizione = null;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
    }

    public function getId(): ?string { return $this->id; }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getData(): ?\DateTimeImmutable
    {
        return $this->data;
    }

    public function setData(\DateTimeImmutable $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getImporto(): ?string
    {
        return $this->importo;
    }

    public function setImporto(string $importo): static
    {
        $this->importo = $importo;

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
}
