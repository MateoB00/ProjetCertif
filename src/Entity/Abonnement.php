<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $enCours;

    #[ORM\Column(type: 'string', length: 255)]
    private $refCommande;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private $forfait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $coach;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $debutAbonnement;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $finAbonnement;

    public function getId(): ?int {
        return $this->id;
    }

    public function getEnCours(): ?bool {
        return $this->enCours;
    }

    public function setEnCours(bool $enCours): self {
        $this->enCours = $enCours;

        return $this;
    }

    public function getRefCommande(): ?string {
        return $this->refCommande;
    }

    public function setRefCommande(string $refCommande): self {
        $this->refCommande = $refCommande;

        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;

        return $this;
    }

    public function getForfait(): ?Produit {
        return $this->forfait;
    }

    public function setForfait(?Produit $forfait): self {
        $this->forfait = $forfait;

        return $this;
    }

    public function getCoach(): ?User {
        return $this->coach;
    }

    public function setCoach(?User $coach): self {
        $this->coach = $coach;

        return $this;
    }

    public function getDebutAbonnement(): ?\DateTimeInterface
    {
        return $this->debutAbonnement;
    }

    public function setDebutAbonnement(?\DateTimeInterface $debutAbonnement): self
    {
        $this->debutAbonnement = $debutAbonnement;

        return $this;
    }

    public function getFinAbonnement(): ?\DateTimeInterface
    {
        return $this->finAbonnement;
    }

    public function setFinAbonnement(?\DateTimeInterface $finAbonnement): self
    {
        $this->finAbonnement = $finAbonnement;

        return $this;
    }
}
