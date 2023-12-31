<?php

namespace App\Entity;


use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Range;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:crud',"pick:crud",'purchase:crud','person:crud'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:crud',"pick:crud",'purchase:crud','person:crud'])]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    #[Groups(['product:crud','purchase:crud'])]
    private ?string $reference = null;

    #[ORM\Column]
    #[Range(min:0)]
    #[Groups(['product:crud',"pick:crud"])]
    private ?int $price = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['product:crud'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    #[Range(min:0)]
    private ?int $stock = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    #[Range(min:0)]
    private ?int $length = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    #[Range(min:0)]
    private ?int $height = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    #[Range(min:0)]
    private ?int $width = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    #[Range(min:0)]
    private ?int $weight = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    #[Range(min:0)]
    private ?int $creationDate = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    private ?bool $isArchived = null;

    #[ORM\Column]
    #[Groups(['product:crud'])]
    private ?bool $isCollector = null;

    #[ORM\ManyToMany(targetEntity: Creator::class, inversedBy: 'products',cascade: ["persist"])]
    #[Groups(['product:crud'])]
    private Collection $creators;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'products',cascade: ["persist"])]
    #[Groups(['product:crud'])]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'products',cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product:crud'])]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'products',cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product:crud'])]
    private ?Editor $editor = null;
    
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Picture::class,cascade: ["persist"])]
    #[Groups(['product:crud','purchase:crud','person:crud','pick:crud'])]
    private Collection $pictures;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Comment::class,cascade: ["persist"])]
    #[Groups(['product:crud'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Pick::class,cascade: ["persist"])]
    #[Groups(['product:crud'])]
    private Collection $picks;

    public function __construct()
    {
        $this->creators = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->picks = new ArrayCollection();
    }

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getCreationDate(): ?int
    {
        return $this->creationDate;
    }

    public function setCreationDate(int $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function isIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function isIsCollector(): ?bool
    {
        return $this->isCollector;
    }

    public function setIsCollector(bool $isCollector): static
    {
        $this->isCollector = $isCollector;

        return $this;
    }

    /**
     * @return Collection<int, Creator>
     */
    public function getCreators(): Collection
    {
        return $this->creators;
    }

    public function addCreator(Creator $creator): static
    {
        if (!$this->creators->contains($creator)) {
            $this->creators->add($creator);
        }

        return $this;
    }

    public function removeCreator(Creator $creator): static
    {
        $this->creators->removeElement($creator);

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setProduct($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture) && ($picture->getProduct() === $this) ) {
            // set the owning side to null (unless already changed)
            $picture->setProduct(null);
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
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment) && ($comment->getProduct() === $this) ) {
            // set the owning side to null (unless already changed)
                $comment->setProduct(null);
        }

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
            $pick->setProduct($this);
        }

        return $this;
    }

    public function removePick(Pick $pick): static
    {
        if ($this->picks->removeElement($pick)) {
            // set the owning side to null (unless already changed)
            if ($pick->getProduct() === $this) {
                $pick->setProduct(null);
            }
        }

        return $this;
    }
}
