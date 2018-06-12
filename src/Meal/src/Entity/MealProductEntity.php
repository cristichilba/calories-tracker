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
    protected $carbs;
    protected $protein;
    protected $fat;

    public function __construct(
        int $mealId = 0,
        int $productId = 0,
        float $quantity = 0,
        float $carbs = 0,
        float $protein = 0,
        float $fat = 0
    ) {
        $this->mealId = $mealId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->carbs = $carbs;
        $this->protein = $protein;
        $this->fat = $fat;
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['mealId'])) {
            return new InvalidArgumentException("Meal id is required");
        }

        if (!isset($data['quantity'])) {
            return new InvalidArgumentException("MealProduct quantity is required");
        }

        if (!isset($data['carbs'])) {
            return new InvalidArgumentException("MealProduct carbs is required");
        }

        if (!isset($data['protein'])) {
            return new InvalidArgumentException("MealProduct protein is required");
        }

        if (!isset($data['fat'])) {
            return new InvalidArgumentException("MealProduct fat is required");
        }

        return new MealProductEntity(
            (int)$data['mealId'],
            (int)$data['productId'],
            (float)$data['quantity'],
            (float)$data['carbs'],
            (float)$data['protein'],
            (float)$data['fat']
        );
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

    /**
     * @return float
     */
    public function getCarbs(): float
    {
        return $this->carbs;
    }

    /**
     * @param float $carbs
     * @return MealProductEntity
     */
    public function setCarbs(float $carbs): MealProductEntity
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
     * @return MealProductEntity
     */
    public function setProtein(float $protein): MealProductEntity
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
     * @return MealProductEntity
     */
    public function setFat(float $fat): MealProductEntity
    {
        $this->fat = $fat;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'mealId' => $this->getMealId(),
            'productId' => $this->getProductId(),
            'quantity' => $this->getQuantity(),
            'carbs' => $this->getCarbs(),
            'protein' => $this->getProtein(),
            'fat' => $this->getFat()
        ];
    }
}
