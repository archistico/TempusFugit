<?php

namespace App\Entity;

use App\Repository\ActionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ActionTypeRepository::class)]
class ActionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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

    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    public function getId(): ?int
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
}
