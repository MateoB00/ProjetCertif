<?php

namespace App\Entity;

use App\Entity\Blog;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PartieblogRepository;

#[ORM\Entity(repositoryClass: PartieblogRepository::class)]
class Partieblog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $titrepartie;

    #[ORM\Column(type: 'text', nullable: true)]
    private $contenupartie;

    #[ORM\ManyToOne(targetEntity: Blog::class, inversedBy: 'partieblog')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $blog;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitrepartie(): ?string
    {
        return $this->titrepartie;
    }

    public function setTitrepartie(?string $titrepartie): self
    {
        $this->titrepartie = $titrepartie;

        return $this;
    }

    public function getContenupartie(): ?string
    {
        return $this->contenupartie;
    }

    public function setContenupartie(?string $contenupartie): self
    {
        $this->contenupartie = $contenupartie;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {
        $this->blog = $blog;

        return $this;
    }
}
