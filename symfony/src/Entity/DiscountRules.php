<?php

namespace App\Entity;

use App\Repository\DiscountRulesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DiscountRulesRepository::class)
 */
class DiscountRules
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="description", type="string", length=255, options={"default"="SPECIAL_DISCOUNT"})
     */
    private $description = 'SPECIAL_DISCOUNT';

    /**
     * @ORM\Column(name="rule_type", type="string", length=100, options={"default"="FREE"})
     */
    private $rule_type = 'FREE';

    /**
     * @ORM\Column(name="category", type="integer", nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(name="discount_type", type="string", length=50, nullable=true)
     */
    private $discount_type;

    /**
     * @ORM\Column(name="discount", type="integer", options={"default"="0"})
     */
    private $discount;

    /**
     * @ORM\Column(name="must", type="string", length=20, nullable=true)
     */
    private $must;

    /**
     * @ORM\Column(name="restraint", type="string", length=100, nullable=true)
     */
    private $restraint;

    /**
     * @ORM\Column(name="operator", type="string", length=5, nullable=true)
     */
    private $operator;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRuleType(): ?string
    {
        return $this->rule_type;
    }

    public function setRuleType(string $rule_type): self
    {
        $this->rule_type = $rule_type;

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

    public function getDiscountType(): ?string
    {
        return $this->discount_type;
    }

    public function setDiscountType(?string $discount_type): self
    {
        $this->discount_type = $discount_type;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getMust(): ?string
    {
        return $this->must;
    }

    public function setMust(?string $must): self
    {
        $this->must = $must;

        return $this;
    }

    public function getRestraint(): ?string
    {
        return $this->restraint;
    }

    public function setRestraint(?string $restraint): self
    {
        $this->restraint = $restraint;

        return $this;
    }

    public function getOperator(): ?string
    {
        return $this->operator;
    }

    public function setOperator(?string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }
}
