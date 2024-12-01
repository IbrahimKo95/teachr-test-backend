<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['products'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['products'])]
    #[Assert\NotBlank(message: "Le nom du produit est obligatoire")]
    #[Assert\Length(min: 2, max: 255,  minMessage: "Le nom du produit doit faire au moins 2 caractères", maxMessage: "Le nom du produit doit faire au maximum 255 caractères")]
    #[Assert\Type(type: 'string', message: "Le nom du produit doit être une chaîne de caractères")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['products'])]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(min: 2, max: 255,  minMessage: "La description du produit doit faire au moins 2 caractères", maxMessage: "La description du produit doit faire au maximum 255 caractères")]
    #[Assert\Type(type: 'string', message: "La description du produit doit être une chaîne de caractères")]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['products'])]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Type(type: 'float', message: "Le prix doit être un nombre décimal")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif")]
    private ?float $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['products'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['products'])]
    private ?Category $category = null;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

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
}
