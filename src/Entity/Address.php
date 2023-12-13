<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: AddressRepository::class)]

class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["person:crud", 'address:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["person:crud", 'address:read'])]
    private ?int $streetNumber = null;

    #[ORM\Column(length: 50)]
    #[Groups(["person:crud", 'address:read'])]
    private ?string $streetName = null;

    #[ORM\Column(length: 50)]
    #[Groups(["person:crud", 'address:read'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(["person:crud", 'address:read'])]
    private ?int $zipcode = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private Person $person;

    #[ORM\OneToMany(mappedBy: 'addresses', targetEntity: Purchase::class)]
    private Collection $purchases;

    public function __construct(int $streetNumber = 0, string $streetName = "", string $city = "", int $zipcode = 00000)
    {
        $this->streetNumber = $streetNumber;
        $this->streetName = $streetName;
        $this->city = $city;
        $this->zipcode = $zipcode;
        $this->purchases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreetNumber(): ?int
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(int $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(string $streetName): static
    {
        $this->streetName = $streetName;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(int $zipcode): static
    {
        $this->zipcode = $zipcode;

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

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setAddresses($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase) && ($purchase->getAddresses() === $this)) {
            // set the owning side to null (unless already changed)
            $purchase->setAddresses(null);
        }

        return $this;
    }
}
