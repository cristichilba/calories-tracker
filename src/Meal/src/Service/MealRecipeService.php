<?php
declare(strict_types=1);

namespace Tracker\Frontend\Meal\Service;

use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Tracker\Frontend\Meal\Entity\MealEntity;
use Tracker\Frontend\Meal\Entity\MealProductEntity;
use Tracker\Frontend\Meal\Entity\MealRecipeEntity;

class MealRecipeService implements MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    /** @var string  */
    protected $entityClass = MealRecipeEntity::class;

    /**
     * @param MealRecipeEntity $entity
     * @param array $options
     * @return MealProductEntity
     */
    public function save($entity, array $options = [])
    {
        if (!$entity instanceof MealRecipeEntity) {
            throw new \InvalidArgumentException('MealRecipeService can save only instances of MealRecipeEntity');
        }
        $mapper = $this->getMapperManager()->get($this->entityClass);
        return $mapper->save($entity, $options);
    }

    public function getMealRecipes($mealId)
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];

        $options['conditions'] += ['mealId' => $mealId];
        $options['conditions'] += ['status' => 'active'];

        $results = $mapper->find('all', $options);
        return $results;
    }

    public function getMealRecipe($mealRecipeId)
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];

        $options['conditions'] += ['id' => $mealRecipeId];

        $results = $mapper->find('all', $options);
        return $results[0] ?? [];
    }
}
