<?php
declare(strict_types=1);

namespace Tracker\Frontend\Meal\Entity;

use Dot\Mapper\Entity\Entity;
use Psr\Log\InvalidArgumentException;

class MealProductEntity extends Entity implements \JsonSerializable
{
    protected $id;
    protected $mealId;
    protected $productId;
    protected $quantity;

    public function __construct(int $mealId = 0, int $productId = 0, float $quantity = 0)
    {
        $this->mealId = $mealId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['mealId'])) {
            return new InvalidArgumentException("Meal id is required");
        }

        if (!isset($data['mealId'])) {
            return new InvalidArgumentException("Product id is required");
        }

        if (!isset($data['quantity'])) {
            return new InvalidArgumentException("Product quantity is required");
        }

        return new MealProductEntity((int)$data['mealId'], (int)$data['productId'], (float)$data['quantity']);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return MealProductEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getMealId()
    {
        return (int)$this->mealId;
    }

    /**
     * @param int $mealId
     * @return MealProductEntity
     */
    public function setMealId($mealId)
    {
        $this->mealId = $mealId;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return (int)$this->productId;
    }

    /**
     * @param int $productId
     * @return MealProductEntity
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return (float)$this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity(float $quantity): MealProductEntity
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'mealId' => $this->getMealId(),
            'productId' => $this->getProductId(),
            'quantity' => $this->getQuantity(),
        ];
    }
}
