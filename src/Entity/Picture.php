<?php

namespace App\Entity;


use App\Repository\PictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PictureRepository::class)]

class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:crud','purchase:crud','person:crud'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['product:crud','purchase:crud','person:crud'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:crud','purchase:crud','person:crud'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['product:crud','purchase:crud','person:crud'])]
    private ?string $alt = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
