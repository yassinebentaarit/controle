<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"le nom du nomA doit etre non vide")]
    #[Groups(['post:read'])]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Article::class, orphanRemoval: true)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<String, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticles(Article $articles): self
    {
        if (!$this->articles->contains($articles)) {
            $this->articles->add($articles);
            $articles->setCategorie($this);
        }

        return $this;
    }

    public function removeArticles(Article $articles): self
    {
        if ($this->articles->removeElement($articles)) {
            // set the owning side to null (unless already changed)
            if ($articles->getCategorie() === $this) {
                $articles->setCategorie(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->libelle . ' (' . $this->id . ')';
    }

}
