<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]

class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('address:read')]
    private ?int $id = null;
    
    #[ORM\Column]
    #[Groups('address:read')]
    private ?int $streetNumber = null;
    
    #[ORM\Column(length: 50)]
    #[Groups('address:read')]
    private ?string $streetName = null;
    
    #[ORM\Column(length: 50)]
    #[Groups('address:read')]
    private ?string $city = null;
    
    #[ORM\Column]
    #[Groups('address:read')]
    private ?int $zipcode = null;

    #[ORM\ManyToMany(targetEntity: Person::class, mappedBy: 'addresses')]
    private Collection $persons;

    #[ORM\OneToMany(mappedBy: 'addresses', targetEntity: Purchase::class)]
    private Collection $purchases;

    public function __construct()
    {
        $this->persons = new ArrayCollection();
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

    /**
     * @return Collection<int, Person>
     */
    public function getPersons(): Collection
    {
        return $this->persons;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->persons->contains($person)) {
            $this->persons->add($person);
            $person->addAddress($this);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        if ($this->persons->removeElement($person)) {
            $person->removeAddress($this);
        }

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
        if ($this->purchases->removeElement($purchase)&&($purchase->getAddresses() === $this)) {
            // set the owning side to null (unless already changed)
                $purchase->setAddresses(null);
        }

        return $this;
    }
}
