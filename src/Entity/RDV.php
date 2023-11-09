<?php

namespace App\Entity;

use App\Repository\RDVRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RDVRepository::class)]
class RDV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['RDV', 'posts:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['RDV', 'posts:read'])]
    #[Assert\NotNull]
    #[Assert\Length(min: 3)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['RDV', 'posts:read'])]
    #[Assert\NotNull(message:"choisir une date s'il vous plait")]
    #[Assert\GreaterThan('today')]
    private ?\DateTimeInterface $dateR = null;



    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['RDV', 'posts:read'])]
    private ?int $idpatient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->dateR;
    }

    public function setDateR(\DateTimeInterface $dateR): self
    {
        $this->dateR = $dateR;

        return $this;
    }

    public function getIdpatient(): ?int
    {
        return $this->idpatient;
    }

    public function setIdpatient(int $idpatient): self
    {
        $this->idpatient = $idpatient;

        return $this;
    }
}
