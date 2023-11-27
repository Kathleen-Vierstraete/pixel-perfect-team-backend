<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]

class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("pick:crud")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDelivery = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateExpectedDelivery = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePurchase = null;

    #[ORM\OneToMany(mappedBy: 'purchase', targetEntity: Pick::class)]
    private Collection $picks;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("pick:crud")]
    private ?Person $person = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("pick:crud")]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Address $addresses = null;

    public function __construct(Person $person = null)
    {
        $this->person = $person;
        $this->picks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDelivery(): ?\DateTimeInterface
    {
        return $this->dateDelivery;
    }

    public function setDateDelivery(?\DateTimeInterface $dateDelivery): static
    {
        $this->dateDelivery = $dateDelivery;

        return $this;
    }

    public function getDateExpectedDelivery(): ?\DateTimeInterface
    {
        return $this->dateExpectedDelivery;
    }

    public function setDateExpectedDelivery(\DateTimeInterface $dateExpectedDelivery): static
    {
        $this->dateExpectedDelivery = $dateExpectedDelivery;

        return $this;
    }

    public function getDatePurchase(): ?\DateTimeInterface
    {
        return $this->datePurchase;
    }

    public function setDatePurchase(\DateTimeInterface $datePurchase): static
    {
        $this->datePurchase = $datePurchase;

        return $this;
    }

    /**
     * @return Collection<int, Pick>
     */
    public function getPicks(): Collection
    {
        return $this->picks;
    }

    public function addPick(Pick $pick): static
    {
        if (!$this->picks->contains($pick)) {
            $this->picks->add($pick);
            $pick->setPurchase($this);
        }

        return $this;
    }

    public function removePick(Pick $pick): static
    {
        if ($this->picks->removeElement($pick)) {
            // set the owning side to null (unless already changed)
            if ($pick->getPurchase() === $this) {
                $pick->setPurchase(null);
            }
        }

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAddresses(): ?Address
    {
        return $this->addresses;
    }

    public function setAddresses(?Address $addresses): static
    {
        $this->addresses = $addresses;

        return $this;
    }
}
