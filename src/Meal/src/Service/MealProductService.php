<?php
declare(strict_types=1);

namespace Tracker\Frontend\Meal\Service;

use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Tracker\Frontend\Meal\Entity\MealEntity;
use Tracker\Frontend\Meal\Entity\MealProductEntity;

class MealProductService implements MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    /** @var string  */
    protected $entityClass = MealProductEntity::class;

    /**
     * @param MealProductEntity $entity
     * @param array $options
     * @return MealProductEntity
     */
    public function save($entity, array $options = [])
    {
        if (!$entity instanceof MealProductEntity) {
            throw new \InvalidArgumentException('MealProductService can save only instances of MealProductEntity');
        }
        $mapper = $this->getMapperManager()->get($this->entityClass);
        return $mapper->save($entity, $options);
    }

    public function getMealProducts($mealId)
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];

        $options['conditions'] += ['mealId' => $mealId];
        $options['conditions'] += ['status' => 'active'];

        $results = $mapper->find('all', $options);
        return $results;
    }

    public function getMealProduct($mealProductId)
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];

        $options['conditions'] += ['id' => $mealProductId];

        $results = $mapper->find('all', $options);
        return $results[0] ?? [];
    }
}
