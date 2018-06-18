<?php
declare (strict_types=1);

namespace Tracker\Frontend\Meal\Mapper;

use Dot\Mapper\Mapper\AbstractDbMapper;

class MealRecipeDbMapper extends AbstractDbMapper
{
    protected $table = 'meal_recipe';

    protected $primaryKey = ['id'];
}
