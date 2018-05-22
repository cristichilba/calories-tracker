<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe\Service;

use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Tracker\Frontend\Recipe\Entity\RecipeEntity;

class RecipeService implements MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    /** @var string  */
    protected $entityClass = RecipeEntity::class;

    /**
     * @param       $id
     * @param array $options
     * @return array
     */
    public function getRecipe($id, $options = [])
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];
        $options['conditions'] += ['id' => $id];

        $result = $mapper->find('all', $options);
        return $result[0] ?? [];
    }

    /**
     * @param       $userId
     * @param array $options
     * @return mixed
     */
    public function getUserRecipes($userId, $options = [])
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];
        $options['conditions'] += ['userId' => $userId];

        $results = $mapper->find('all', $options);
        return $results;
    }
}
