<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal\Mapper;

use Dot\Mapper\Mapper\AbstractDbMapper;

class MealDbMapper extends AbstractDbMapper
{
    /** @var string  */
    protected $table = 'meal';
    /** @var string  */
    protected $primaryKey = ['id'];
}
