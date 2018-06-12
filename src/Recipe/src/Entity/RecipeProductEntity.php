<?php
declare(strict_types=1);

namespace Tracker\Frontend\Recipe\Entity;

use Dot\Mapper\Entity\Entity;

class RecipeProductEntity extends Entity implements \JsonSerializable
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $recipeId;
    /** @var int */
    protected $productId;
    /** @var  float */
    protected $quantity;

    public function __construct(
        int $recipeId = 0,
        int $productId = 0,
        float $quantity = 0
    ) {
        $this->recipeId = $recipeId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['recipeId'])) {
            return new \InvalidArgumentException('RecipeProduct recipeId is required.');
        }
        if (!isset($data['productId'])) {
            return new \InvalidArgumentException('RecipeProduct productId is required.');
        }

        return new RecipeProductEntity((int)$data['recipeId'], (int)$data['productId'], (float)$data['quantity']);
    }

    public static function emptyRecipeProduct()
    {
        return new RecipeProductEntity(0, 0, 0);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return RecipeProductEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecipeId()
    {
        return (int)$this->recipeId;
    }

    /**
     * @param int $recipeId
     * @return RecipeProductEntity
     */
    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
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
     * @param mixed $productId
     * @return RecipeProductEntity
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return (float)$this->quantity;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'recipeId' => $this->getrecipeId(),
            'productId' => $this->getRecipeId(),
            'quantity' => $this->getQuantity(),
        ];
    }
}
