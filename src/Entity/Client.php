<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denominazione = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $piva = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telefono = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $via = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cap = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $citta = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'client')]
    private Collection $projects;

    /**
     * @var Collection<int, Communication>
     */
    #[ORM\OneToMany(targetEntity: Communication::class, mappedBy: 'client')]
    private Collection $communications;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'cliente')]
    private Collection $users;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122(); 
        $this->projects = new ArrayCollection();
        $this->communications = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getDenominazione() ?: 'Cliente #'.$this->getId();
    }

    public function getId(): ?string { return $this->id; }

    public function getDenominazione(): ?string
    {
        return $this->denominazione;
    }

    public function setDenominazione(string $denominazione): static
    {
        $this->denominazione = $denominazione;

        return $this;
    }

    public function getPiva(): ?string
    {
        return $this->piva;
    }

    public function setPiva(?string $piva): static
    {
        $this->piva = $piva;

        return $this;
    }

    public function getCf(): ?string
    {
        return $this->cf;
    }

    public function setCf(?string $cf): static
    {
        $this->cf = $cf;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getVia(): ?string
    {
        return $this->via;
    }

    public function setVia(?string $via): static
    {
        $this->via = $via;

        return $this;
    }

    public function getCap(): ?string
    {
        return $this->cap;
    }

    public function setCap(?string $cap): static
    {
        $this->cap = $cap;

        return $this;
    }

    public function getCitta(): ?string
    {
        return $this->citta;
    }

    public function setCitta(?string $citta): static
    {
        $this->citta = $citta;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
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
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setClient($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getClient() === $this) {
                $project->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Communication>
     */
    public function getCommunications(): Collection
    {
        return $this->communications;
    }

    public function addCommunication(Communication $communication): static
    {
        if (!$this->communications->contains($communication)) {
            $this->communications->add($communication);
            $communication->setClient($this);
        }

        return $this;
    }

    public function removeCommunication(Communication $communication): static
    {
        if ($this->communications->removeElement($communication)) {
            // set the owning side to null (unless already changed)
            if ($communication->getClient() === $this) {
                $communication->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCliente($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCliente() === $this) {
                $user->setCliente(null);
            }
        }

        return $this;
    }

}
