<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: "products")]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products', cascade: ['persist'])]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPhoto::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $productPhotos;

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        private ?string $name = null,

        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        private ?string $description = null,

        #[ORM\Column(type: Types::TEXT)]
        #[Assert\NotBlank]
        #[Assert\Length(min: 30, minMessage: 'Este campo deve possuir no mínimo 30 caracteres.')]
        private ?string $body = null,

        #[ORM\Column]
        #[Assert\NotBlank]
        private ?int $price = null,

        #[ORM\Column(length: 255)]
        #[Gedmo\Slug(fields: ['name'])]
        private ?string $slug = null,

        #[ORM\Column]
        private ?\DateTimeImmutable $createdAt = new \DateTimeImmutable(),

        #[ORM\Column(nullable: true)]
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->categories = new ArrayCollection();
        $this->productPhotos = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, ProductPhoto>
     */
    public function getProductPhotos(): Collection
    {
        return $this->productPhotos;
    }

    public function addProductPhoto(ProductPhoto $productPhoto): static
    {
        if (!$this->productPhotos->contains($productPhoto)) {
            $this->productPhotos->add($productPhoto);
            $productPhoto->setProduct($this);
        }

        return $this;
    }

    public function removeProductPhoto(ProductPhoto $productPhoto): static
    {
        if ($this->productPhotos->removeElement($productPhoto)) {
            // set the owning side to null (unless already changed)
            if ($productPhoto->getProduct() === $this) {
                $productPhoto->setProduct(null);
            }
        }

        return $this;
    }

    public function addManyProductPhotos(array $productPhotos): self
    {
        foreach ($productPhotos as $productPhoto) {
            $this->addProductPhoto($productPhoto);
        }
        return $this;
    }
}
