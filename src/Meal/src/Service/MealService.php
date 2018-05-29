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

class MealService implements MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    /** @var string  */
    protected $entityClass = MealEntity::class;

    public function getMealsOnDateByType(\DateTime $date, $type, $options = [])
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];
        $options['conditions'] += ['date' => $date->format('Y-m-d')];
        $options['conditions'] += ['type' => $type];

        $results = $mapper->find('all', $options);
        return $results;
    }
}
