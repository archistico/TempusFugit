<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Action $projectAction = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Assert\Expression(
        "this.getEndAt() === null or this.getEndAt() >= this.getStartAt()",
        message: "La fine deve essere successiva all'inizio."
    )]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $durataMin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descrizione = null;

    #[ORM\Column(nullable: true)]
    private ?bool $billable = null;

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

    public function getProjectAction(): ?Action
    {
        return $this->projectAction;
    }

    public function setProjectAction(?Action $projectAction): static
    {
        $this->projectAction = $projectAction;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;
        $this->recalcDuration();
        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;
        $this->recalcDuration();
        return $this;
    }

    public function getDurataMin(): ?int
    {
        return $this->durataMin;
    }

    public function setDurataMin(?int $durataMin): static
    {
        $this->durataMin = $durataMin;

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

    public function isBillable(): ?bool
    {
        return $this->billable;
    }

    public function setBillable(?bool $billable): static
    {
        $this->billable = $billable;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function recalcDuration(): void
    {
        if (!$this->startAt || !$this->endAt) {
            $this->durataMin = 0;
            return;
        }
        // se end < start, forza a 0 (oppure lancia validazione)
        if ($this->endAt < $this->startAt) {
            $this->durataMin = 0;
            return;
        }
        $diff = $this->endAt->getTimestamp() - $this->startAt->getTimestamp();
        $this->durataMin = (int) floor($diff / 60);
    }
}
