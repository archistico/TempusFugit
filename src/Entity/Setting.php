<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $chiave = null;

    #[ORM\Column(length: 255)]
    private ?string $valore = null;

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
