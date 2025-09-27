<?php

namespace App\Entity;

use App\Repository\ActionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: ActionTypeRepository::class)]
class ActionType
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
	private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descrizione = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colore = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icona = null;

    #[ORM\Column]
    private ?bool $fatturabileDefault = null;

    /**
     * @var Collection<int, Action>
     */
    #[ORM\OneToMany(targetEntity: Action::class, mappedBy: 'type')]
    private Collection $actions;

    /**
     * @var Collection<int, ProjectTypeActionTemplate>
     */
    #[ORM\OneToMany(targetEntity: ProjectTypeActionTemplate::class, mappedBy: 'actionType')]
    private Collection $projectTypeActionTemplates;

    public function __construct()
    {
        $this->id = Uuid::v7(); 
        $this->actions = new ArrayCollection();
        $this->projectTypeActionTemplates = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getDescrizione() ?? '';
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

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }

    public function setDescrizione(string $descrizione): static
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    public function getColore(): ?string
    {
        return $this->colore;
    }

    public function setColore(?string $colore): static
    {
        $this->colore = $colore;

        return $this;
    }

    public function getIcona(): ?string
    {
        return $this->icona;
    }

    public function setIcona(?string $icona): static
    {
        $this->icona = $icona;

        return $this;
    }

    public function isFatturabileDefault(): ?bool
    {
        return $this->fatturabileDefault;
    }

    public function setFatturabileDefault(bool $fatturabileDefault): static
    {
        $this->fatturabileDefault = $fatturabileDefault;

        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setType($this);
        }

        return $this;
    }

    public function removeAction(Action $action): static
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getType() === $this) {
                $action->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProjectTypeActionTemplate>
     */
    public function getProjectTypeActionTemplates(): Collection
    {
        return $this->projectTypeActionTemplates;
    }

    public function addProjectTypeActionTemplate(ProjectTypeActionTemplate $projectTypeActionTemplate): static
    {
        if (!$this->projectTypeActionTemplates->contains($projectTypeActionTemplate)) {
            $this->projectTypeActionTemplates->add($projectTypeActionTemplate);
            $projectTypeActionTemplate->setActionType($this);
        }

        return $this;
    }

    public function removeProjectTypeActionTemplate(ProjectTypeActionTemplate $projectTypeActionTemplate): static
    {
        if ($this->projectTypeActionTemplates->removeElement($projectTypeActionTemplate)) {
            // set the owning side to null (unless already changed)
            if ($projectTypeActionTemplate->getActionType() === $this) {
                $projectTypeActionTemplate->setActionType(null);
            }
        }

        return $this;
    }
}
