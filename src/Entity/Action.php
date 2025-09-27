<?php

namespace App\Entity;

use App\Repository\ActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
#[ORM\Table(name: '`action`')]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\Column(length: 255)]
    private ?string $titolo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descrizione = null;

    #[ORM\Column]
    private ?int $stimaMin = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $deadline = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActionType $type = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActionStatus $status = null;

    #[ORM\Column]
    private ?bool $fatturabile = null;

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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

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

    public function getStimaMin(): ?int
    {
        return $this->stimaMin;
    }

    public function setStimaMin(int $stimaMin): static
    {
        $this->stimaMin = $stimaMin;

        return $this;
    }

    public function getDeadline(): ?\DateTime
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTime $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getType(): ?ActionType
    {
        return $this->type;
    }

    public function setType(?ActionType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?ActionStatus
    {
        return $this->status;
    }

    public function setStatus(?ActionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isFatturabile(): ?bool
    {
        return $this->fatturabile;
    }

    public function setFatturabile(bool $fatturabile): static
    {
        $this->fatturabile = $fatturabile;

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
