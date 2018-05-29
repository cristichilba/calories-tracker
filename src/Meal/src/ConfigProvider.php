<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal;

use Dot\Mapper\Factory\DbMapperFactory;
use Tracker\Frontend\Meal\Entity\MealEntity;
use Tracker\Frontend\Meal\Fieldset\ProductFieldset;
use Tracker\Frontend\Meal\Form\ProductForm;
use Tracker\Frontend\Meal\Mapper\MealDbMapper;
use Tracker\Frontend\Meal\Service\MealService;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package Tracker\Frontend\Meal
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'dot_mapper' => $this->getMappers(),
            'dot_form' => $this->getForms(),
            'templates' => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                MealService::class => InvokableFactory::class,
            ],
            'aliases' => [
                'MealService' => MealService::class,
            ]
        ];
    }

    public function getMappers()
    {
        return [
            'mapper_manager' => [
                'factories' => [
                    MealDbMapper::class => DbMapperFactory::class,
                ],
                'aliases' => [
                    MealEntity::class => MealDbMapper::class,
                ]
            ],
        ];
    }

    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories' => [
                    ProductFieldset::class => InvokableFactory::class,
                    ProductForm::class     => InvokableFactory::class,
                ],
                'aliases' => [
                    'ProductFieldset' => ProductFieldset::class,
                    'Product' => ProductForm::class,
                ]
            ]
        ];
    }
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'meal' => [__DIR__ . '/../templates/meal']
            ]
        ];
    }
}
