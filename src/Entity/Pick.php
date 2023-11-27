<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PickRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PickRepository::class)]
class Pick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("pick:crud")]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups("pick:crud")]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups("pick:crud")]
    private ?int $priceitem = null;

    #[ORM\ManyToOne(inversedBy: 'picks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("pick:crud")]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'picks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("pick:crud")]
    private ?Purchase $purchase = null;

    public function __construct(Product $product, int $quantity,Purchase $purchase)
    {
        $this->product = $product;
        $this->priceitem = $product->getPrice();
        $this->quantity = $quantity;
        $this->purchase = $purchase;
    }

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
