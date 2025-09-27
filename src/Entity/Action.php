<?php

namespace App\Entity;

use App\Repository\ActionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
#[ORM\Table(name: '`action`')]
class Action
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\Column(length: 255)]
    private ?string $titolo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descrizione = null;

    #[ORM\Column]
    private ?int $stimaMin = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deadline = null;

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

    /**
     * @var Collection<int, TimeEntry>
     */
    #[ORM\OneToMany(targetEntity: TimeEntry::class, mappedBy: 'projectAction')]
    private Collection $timeEntries;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->timeEntries = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitolo() ?? '';
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

    public function getDeadline(): ?\DateTimeImmutable
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeImmutable $deadline): static
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

    /**
     * @return Collection<int, TimeEntry>
     */
    public function getTimeEntries(): Collection
    {
        return $this->timeEntries;
    }

    public function addTimeEntry(TimeEntry $timeEntry): static
    {
        if (!$this->timeEntries->contains($timeEntry)) {
            $this->timeEntries->add($timeEntry);
            $timeEntry->setProjectAction($this);
        }

        return $this;
    }

    public function removeTimeEntry(TimeEntry $timeEntry): static
    {
        if ($this->timeEntries->removeElement($timeEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeEntry->getProjectAction() === $this) {
                $timeEntry->setProjectAction(null);
            }
        }

        return $this;
    }
}
