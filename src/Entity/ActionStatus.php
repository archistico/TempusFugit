<?php

namespace App\Entity;

use App\Repository\ActionStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ActionStatusRepository::class)]
class ActionStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descrizione = null;

    #[ORM\Column]
    private ?int $ordine = null;

    #[ORM\Column]
    private ?bool $chiusura = null;

    /**
     * @var Collection<int, Action>
     */
    #[ORM\OneToMany(targetEntity: Action::class, mappedBy: 'status')]
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

    public function getOrdine(): ?int
    {
        return $this->ordine;
    }

    public function setOrdine(int $ordine): static
    {
        $this->ordine = $ordine;

        return $this;
    }

    public function isChiusura(): ?bool
    {
        return $this->chiusura;
    }

    public function setChiusura(bool $chiusura): static
    {
        $this->chiusura = $chiusura;

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
            $action->setStatus($this);
        }

        return $this;
    }

    public function removeAction(Action $action): static
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getStatus() === $this) {
                $action->setStatus(null);
            }
        }

        return $this;
    }
}
