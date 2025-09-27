<?php

namespace App\Entity;

use App\Repository\ProjectTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: ProjectTypeRepository::class)]
class ProjectType
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
	private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descrizione = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $costoOrarioDefault = null;

    #[ORM\Column]
    private ?int $version = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'type')]
    private Collection $projects;

    /**
     * @var Collection<int, ProjectTypeActionTemplate>
     */
    #[ORM\OneToMany(targetEntity: ProjectTypeActionTemplate::class, mappedBy: 'projectType')]
    private Collection $projectTypeActionTemplates;

    public function __construct()
    {
        $this->id = Uuid::v7(); 
        $this->projects = new ArrayCollection();
        $this->projectTypeActionTemplates = new ArrayCollection();
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

    public function getCostoOrarioDefault(): ?string
    {
        return $this->costoOrarioDefault;
    }

    public function setCostoOrarioDefault(?string $costoOrarioDefault): static
    {
        $this->costoOrarioDefault = $costoOrarioDefault;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

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
            $project->setType($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getType() === $this) {
                $project->setType(null);
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
            $projectTypeActionTemplate->setProjectType($this);
        }

        return $this;
    }

    public function removeProjectTypeActionTemplate(ProjectTypeActionTemplate $projectTypeActionTemplate): static
    {
        if ($this->projectTypeActionTemplates->removeElement($projectTypeActionTemplate)) {
            // set the owning side to null (unless already changed)
            if ($projectTypeActionTemplate->getProjectType() === $this) {
                $projectTypeActionTemplate->setProjectType(null);
            }
        }

        return $this;
    }
}
