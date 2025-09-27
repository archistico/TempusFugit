<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $chiave = null;

    #[ORM\Column(length: 255)]
    private ?string $valore = null;

    public function __construct()
    {
        $this->id = Uuid::v7()->toRfc4122(); 
    }

    public function getId(): ?string { return $this->id; }

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
