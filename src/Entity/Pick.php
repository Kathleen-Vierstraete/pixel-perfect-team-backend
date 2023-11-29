<?php

namespace App\Entity;


use App\Repository\PickRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PickRepository::class)]

class Pick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:crud'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    private ?int $priceitem = null;

    #[ORM\ManyToOne(inversedBy: 'picks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'picks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Purchase $purchase = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPriceitem(): ?int
    {
        return $this->priceitem;
    }

    public function setPriceitem(int $priceitem): static
    {
        $this->priceitem = $priceitem;

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

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): static
    {
        $this->purchase = $purchase;

        return $this;
    }
}
