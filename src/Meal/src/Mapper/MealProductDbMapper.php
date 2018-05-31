<?php
declare (strict_types=1);

namespace Tracker\Frontend\Meal\Mapper;

use Dot\Mapper\Mapper\AbstractDbMapper;

class MealProductDbMapper extends AbstractDbMapper
{
    protected $table = 'meal_product';

    protected $primaryKey = ['id'];
}
