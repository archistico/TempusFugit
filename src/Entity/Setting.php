<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
	private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $chiave = null;

    #[ORM\Column(length: 255)]
    private ?string $valore = null;

    public function __construct()
    {
        $this->id = Uuid::v7(); 
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

    public function getChiave(): ?string
    {
        return $this->chiave;
    }

    public function setChiave(string $chiave): static
    {
        $this->chiave = $chiave;

        return $this;
    }

    public function getValore(): ?string
    {
        return $this->valore;
    }

    public function setValore(string $valore): static
    {
        $this->valore = $valore;

        return $this;
    }
}
