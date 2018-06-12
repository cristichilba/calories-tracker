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

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'mealId' => $this->getMealId(),
            'recipeId' => $this->getRecipeId(),
        ];
    }
}
