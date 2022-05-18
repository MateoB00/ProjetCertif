<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BlogRepository;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre;

    #[ORM\Column(type: 'text')]
    private $contenu;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $date;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'blogs')]
    private $categorie;

    #[ORM\Column(type: 'text')]
    private $image;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'blogs')]
    private $auteur;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Partieblog::class, orphanRemoval: true)]
    private $partieblog;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Commentaire::class, orphanRemoval: true)]
    private $commentaires;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->partieblog = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * @return Collection<int, Partieblog>
     */
    public function getPartieblog(): Collection
    {
        return $this->partieblog;
    }

    public function addPartieblog(Partieblog $partieblog): self
    {
        if (!$this->partieblog->contains($partieblog)) {
            $this->partieblog[] = $partieblog;
            $partieblog->setBlog($this);
        }

        return $this;
    }

    public function removePartieblog(Partieblog $partieblog): self
    {
        if ($this->partieblog->removeElement($partieblog)) {
            // set the owning side to null (unless already changed)
            if ($partieblog->getBlog() === $this) {
                $partieblog->setBlog(null);
            }
        }

        return $this;
    }

    public function resume(): string
    {

        if (strlen($this->contenu) > 5)
            $resume = substr($this->contenu, 0, 5) . '...';
        else $resume = $this->contenu;

        return $resume;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setBlog($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getBlog() === $this) {
                $commentaire->setBlog(null);
            }
        }

        return $this;
    }
}
