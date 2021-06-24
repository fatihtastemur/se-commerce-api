<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="product_id", type="integer", options={"default"="0"})
     */
    private $product_id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(name="category", type="integer", options={"default"="0"})
     */
    private $category;

    /**
     * @ORM\Column(name="price", type="float", options={"default"="0"})
     */
    private $price;

    /**
     * @ORM\Column(name="stock", type="integer", options={"default"="0"})
     */
    private $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function setProductId($product_id): void
    {
        $this->product_id = $product_id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
