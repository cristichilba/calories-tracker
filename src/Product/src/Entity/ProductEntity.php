<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Product\Entity;

use Dot\Mapper\Entity\Entity;

/**
 * Class ProductEntity
 * @package Tracker\Frontend\Product\Entity
 */
class ProductEntity extends Entity implements \JsonSerializable
{
    /** @var  int */
    protected $id;

    /** @var  string */
    protected $title;

    /** @var  float */
    protected $carbs;

    /** @var  float */
    protected $protein;

    /** @var  float */
    protected $fat;

    /** @var  string */
    protected $dateCreated;

    /** @var string */
    protected $status;

    /**
     * ProductEntity constructor.
     *
     * @param string $title
     * @param float  $carbs
     * @param float  $protein
     * @param float  $fat
     * @param string $status
     */
    public function __construct(
        string $title = "",
        float $carbs = 0,
        float $protein = 0,
        float $fat = 0,
        string $status = "active"
    ) {
        $this->title = $title;
        $this->carbs = $carbs;
        $this->protein = $protein;
        $this->fat = $fat;
        $this->status = $status;
    }

    /**
     * Builds a product entity instance from an array
     * @param array $product
     * @return ProductEntity
     */
    public static function fromArray(array $product): ProductEntity
    {
        if (!isset($product['title'])) {
            throw new \InvalidArgumentException('Product title is required.');
        }

        return new ProductEntity(
            $product['title'],
            (float)$product['carbs'] ?? 0,
            (float)$product['protein'] ?? 0,
            (float)$product['fat'] ?? 0,
            $product['status'] ?? "pending"
        );
    }

    /**
     * Builds a product entity instance from an array
     * @param ProductEntity $product
     * @return ProductEntity
     */
    public static function fromObject(ProductEntity $product): ProductEntity
    {
        return new ProductEntity(
            $product->getTitle(),
            (float)$product->getCarbs() ?? 0,
            (float)$product->getProtein() ?? 0,
            (float)$product->getFat() ?? 0,
            $product->getStatus() ?? "pending"
        );
    }

    /**
     * Returns an empty instance of a product
     * @return ProductEntity
     */
    public static function emptyProduct(): ProductEntity
    {
        return new ProductEntity("");
    }

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ProductEntity
     */
    public function setId(int $id): ProductEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ProductEntity
     */
    public function setTitle(string $title): ProductEntity
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return float
     */
    public function getCarbs(): float
    {
        return $this->carbs;
    }

    /**
     * @param float $carbs
     * @return ProductEntity
     */
    public function setCarbs(float $carbs): ProductEntity
    {
        $this->carbs = $carbs;
        return $this;
    }

    /**
     * @return float
     */
    public function getProtein(): float
    {
        return $this->protein;
    }

    /**
     * @param float $protein
     * @return ProductEntity
     */
    public function setProtein(float $protein): ProductEntity
    {
        $this->protein = $protein;
        return $this;
    }

    /**
     * @return float
     */
    public function getFat(): float
    {
        return $this->fat;
    }

    /**
     * @param float $fat
     * @return ProductEntity
     */
    public function setFat(float $fat): ProductEntity
    {
        $this->fat = $fat;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDateCreated(): ?string
    {
        return $this->dateCreated;
    }

    /**
     * @param string $dateCreated
     * @return ProductEntity
     */
    public function setDateCreated(string $dateCreated): ProductEntity
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return ProductEntity
     */
    public function setStatus(string $status): ProductEntity
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'carbs' => $this->getCarbs(),
            'protein' => $this->getProtein(),
            'fat' => $this->getFat(),
            'dateCreated' => $this->getDateCreated(),
            'status' => $this->getStatus(),
        ];
    }
}
