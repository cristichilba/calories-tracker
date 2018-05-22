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
use Zend\Db\Sql\Select;

class RecipeDbMapper extends AbstractDbMapper
{
    /** @var string  */
    protected $table = 'recipe';

    /** @var array  */
    protected $primaryKey = ['id'];
}
