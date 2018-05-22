<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Product\Mapper;

use Admin\App\Mapper\SearchFinderMapperTrait;
use Dot\Mapper\Mapper\AbstractDbMapper;

class ProductDbMapper extends AbstractDbMapper
{
    /** @var string */
    protected $table = 'product';

    /** @var string  */
    protected $primaryKey = ['id'];
}
