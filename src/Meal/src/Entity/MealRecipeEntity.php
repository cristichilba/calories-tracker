<?php
declare(strict_types=1);

namespace Tracker\Frontend\Meal\Entity;

use Dot\Mapper\Entity\Entity;

class MealRecipeEntity extends Entity implements \JsonSerializable
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $mealId;
    /** @var int */
    protected $recipeId;
    /** @var string  */
    protected $status;

    public function __construct(
        int $mealId = 0,
        int $recipeId = 0,
        string $status = 'active'
    ) {
        $this->mealId = $mealId;
        $this->recipeId = $recipeId;
        $this->status = $status;
    }

    /**
     * @param array $data
     * @return \InvalidArgumentException|MealRecipeEntity
     */
    public static function fromArray(array $data)
    {
        if (!isset($data['mealId'])) {
            return new \InvalidArgumentException("Meal id is required");
        }

        if (!isset($data['recipeId'])) {
            return new \InvalidArgumentException("Recipe id is required");
        }

        $status = $data['status'] ?? "active";

        return new MealRecipeEntity(
            (int)$data['mealId'],
            (int)$data['recipeId'],
            (string)$status
        );
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
     * @return MealRecipeEntity
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
     * @return MealRecipeEntity
     */
    public function setMealId($mealId)
    {
        $this->mealId = $mealId;
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
     * @param mixed $recipeId
     * @return MealRecipeEntity
     */
    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
        return $this;
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->status;
    }

    /**
     * @param $status
     * @return MealRecipeEntity
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'mealId' => $this->getMealId(),
            'recipeId' => $this->getRecipeId(),
            'status' => $this->getStatus(),
        ];
    }
}
