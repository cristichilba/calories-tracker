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
use Tracker\Frontend\Recipe\Entity\RecipeProductEntity;
use Tracker\Frontend\Recipe\Entity\RecipeEntity;

class RecipeProductService implements MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    /** @var string  */
    protected $entityClass = RecipeProductEntity::class;

    /**
     * @param RecipeProductEntity $entity
     * @param array               $options
     * @return RecipeProductEntity
     */
    public function save($entity, array $options = [])
    {
        if (!$entity instanceof RecipeProductEntity) {
            throw new \InvalidArgumentException('RecipeProductService can save only instances of RecipeProductEntity');
        }
        $mapper = $this->getMapperManager()->get($this->entityClass);
        return $mapper->save($entity, $options);
    }

    /**
     * @param       $id
     * @param array $options
     * @return array
     */
    public function getRecipeProduct($id, $options = [])
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];
        $options['conditions'] += ['id' => $id];

        $result = $mapper->find('all', $options);
        return $result[0] ?? [];
    }

    /**
     * @param $recipeId
     * @return mixed
     */
    public function getRecipeProducts($recipeId)
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];
        $options['conditions'] += ['recipeId' => $recipeId];
        $options['conditions'] += ['status' => 'active'];

        $results = $mapper->find('all', $options);
        return $results;
    }
}
