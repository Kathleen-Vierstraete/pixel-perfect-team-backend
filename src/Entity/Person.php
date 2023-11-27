<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("person:crud")]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups("person:crud")]
    private ?string $lastName = null;

    #[ORM\Column(length: 50)]
    #[Groups("person:crud")]
    private ?string $firstName = null;

    #[ORM\Column(length: 10)]
    #[Groups("person:crud")]
    private ?string $phoneNumber = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Purchase::class)]
    private Collection $purchases;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups("person:crud")]
    private ?Credential $credential = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Contact::class)]
    private Collection $contacts;

    #[ORM\ManyToMany(targetEntity: Address::class, inversedBy: 'persons')]
    #[Groups("person:crud")]
    private Collection $addresses;

    public function __construct(string $firstName,string $lastName, string $phoneNumber)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->purchases = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

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
            $purchase->setPerson($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase) && ($purchase->getPerson() === $this)) {
            // set the owning side to null (unless already changed)
            $purchase->setPerson(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPerson($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment) && ($comment->getPerson() === $this)) {
            // set the owning side to null (unless already changed)
            $comment->setPerson(null);
        }

        return $this;
    }

    public function getcredential(): ?Credential
    {
        return $this->credential;
    }

    public function setcredential(Credential $credential): static
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setPerson($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact) && ($contact->getPerson() === $this)) {
            // set the owning side to null (unless already changed)
            $contact->setPerson(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        $this->addresses->removeElement($address);

        return $this;
    }
}
