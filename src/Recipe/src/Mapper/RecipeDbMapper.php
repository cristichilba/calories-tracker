<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe\Mapper;

use Dot\Mapper\Event\MapperEvent;
use Dot\Mapper\Mapper\AbstractDbMapper;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Select;

class RecipeDbMapper extends AbstractDbMapper
{
    /** @var string  */
    protected $table = 'recipe';

    /** @var array  */
    protected $primaryKey = ['id'];

    public function searchRecipesByTitle($searchTerm, $type = 'all', $options = [])
    {
        $select = $this->getSlaveSql()->select()->from(['Recipe' => 'recipe']);
        $select->where([
            new Like('name', $searchTerm.'%'),
            'status' => 'active',
        ]);

        $event = $this->dispatchEvent(
            MapperEvent::EVENT_MAPPER_BEFORE_FIND,
            ['select' => $select, 'type' => $type, 'options' => $options]
        );

        if ($event->stopped()) {
            return $event->last();
        }
        $stmt = $this->getSlaveSql()->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
            $resultSet->initialize($result);
            $entities = $this->loadAll($resultSet, $options);

            $this->dispatchEvent(
                MapperEvent::EVENT_MAPPER_AFTER_FIND,
                ['entities' => $entities, 'type' => $type, 'options' => $options]
            );

            return $entities;
        }

        return [];
    }
}
