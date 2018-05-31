<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal\Service;

use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Tracker\Frontend\Meal\Entity\MealEntity;
use Tracker\Frontend\Meal\Entity\MealProductEntity;
use Tracker\Frontend\Product\Entity\ProductEntity;

class MealService implements MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    /** @var string  */
    protected $entityClass = MealEntity::class;
    protected $mealProductClass = MealProductEntity::class;

    public function getMealOnDateByType($date, $type, $options = [])
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];
        $options['conditions'] += ['date' => $date->format('Y-m-d')];
        $options['conditions'] += ['type' => $type];

        $results = $mapper->find('all', $options);
        return $results[0] ?? null;
    }


    /**
     * @param MealEntity $entity
     * @param array $options
     * @return MealEntity
     */
    public function save($entity, array $options = [])
    {
        if (!$entity instanceof MealEntity) {
            throw new \InvalidArgumentException('MealService can save only instances of MealEntity');
        }
        $mapper = $this->getMapperManager()->get($this->entityClass);
        return $mapper->save($entity, $options);
    }
}
