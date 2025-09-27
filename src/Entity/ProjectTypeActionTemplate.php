<?php

namespace App\Entity;

use App\Repository\ProjectTypeActionTemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: ProjectTypeActionTemplateRepository::class)]
class ProjectTypeActionTemplate
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
	private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'projectTypeActionTemplates')]
    private ?ProjectType $projectType = null;

    #[ORM\Column(length: 255)]
    private ?string $titolo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descrizione = null;

    #[ORM\Column]
    private ?int $stimaMin = null;

    #[ORM\ManyToOne(inversedBy: 'projectTypeActionTemplates')]
    private ?ActionType $actionType = null;

    #[ORM\ManyToOne(inversedBy: 'projectTypeActionTemplates')]
    private ?ActionStatus $status = null;

    #[ORM\Column]
    private ?int $ordine = null;

    public function __construct()
    {
        $this->id = Uuid::v7(); 
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getProjectType(): ?ProjectType
    {
        return $this->projectType;
    }

    public function setProjectType(?ProjectType $projectType): static
    {
        $this->projectType = $projectType;

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

    public function getActionType(): ?ActionType
    {
        return $this->actionType;
    }

    public function setActionType(?ActionType $actionType): static
    {
        $this->actionType = $actionType;

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

    public function getOrdine(): ?int
    {
        return $this->ordine;
    }

    public function setOrdine(int $ordine): static
    {
        $this->ordine = $ordine;

        return $this;
    }
}
