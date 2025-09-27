<?php

namespace App\Entity;

use App\Repository\CommunicationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CommunicationRepository::class)]
class Communication
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'communications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'communications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $data = null;

    #[ORM\Column(length: 255)]
    private ?string $comunicazione = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tipologia = null;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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

    public function getComunicazione(): ?string
    {
        return $this->comunicazione;
    }

    public function setComunicazione(string $comunicazione): static
    {
        $this->comunicazione = $comunicazione;

        return $this;
    }

    public function getTipologia(): ?string
    {
        return $this->tipologia;
    }

    public function setTipologia(?string $tipologia): static
    {
        $this->tipologia = $tipologia;

        return $this;
    }
}
