<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Product\Service;

use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Zend\Db\Sql\Select;

/**
 * Class ProductService
 * @package Tracker\Frontend\Product\Service
 */
class ProductService implements MapperManagerAwareInterface
{
    use MapperManagerAwareTrait;

    protected $entityClass = ProductEntity::class;
    
    public function getProduct($id, $options = [])
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['conditions'] = $options['conditions'] ?? [];
        $options['conditions'] += ['id' => $id];

        $result = $mapper->find('all', $options);
        return $result[0] ?? [];
    }

    public function getRecipeProducts($recipeId, $options = [])
    {
        $mapper = $this->getMapperManager()->get($this->entityClass);
        $options['joins'] = $options['joins'] ?? [];
        $options['joins'] += [
            'ProductRecipe' => [
                'table' => 'product_recipe',
                'on' => 'ProductRecipe.productId = Product.id',
                'type' => Select::JOIN_INNER,
            ],
        ];

        $results = $mapper->find('all', $options);
        return $results;
    }

    /**
     * @param ProductEntity $entity
     * @param array $options
     * @return ProductEntity
     */
    public function save($entity, array $options = [])
    {
        if (!$entity instanceof ProductEntity) {
            throw new \InvalidArgumentException('ProductService can save only instances of ProductEntity');
        }
        $mapper = $this->getMapperManager()->get($this->entityClass);
        return $mapper->save($entity, $options);
    }
}
