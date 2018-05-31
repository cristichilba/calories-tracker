<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe;

use Dot\Mapper\Factory\DbMapperFactory;
use Tracker\Frontend\Recipe\Entity\RecipeEntity;
use Tracker\Frontend\Recipe\Mapper\RecipeDbMapper;
use Tracker\Frontend\Recipe\Service\RecipeService;
use Zend\ServiceManager\Factory\InvokableFactory;

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
                RecipeService::class => InvokableFactory::class,
            ],
            'aliases' => [
                'RecipeService' => RecipeService::class,
            ]
        ];
    }

    public function getMappers()
    {
        return [
            'mapper_manager' => [
                'factories' => [
                    RecipeDbMapper::class => DbMapperFactory::class,
                ],
                'aliases' => [
                    RecipeEntity::class => RecipeDbMapper::class,
                ]
            ],
        ];
    }

    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories' => [
//                    ProductFieldset::class => InvokableFactory::class,
//                    ProductForm::class     => InvokableFactory::class,
                ],
                'aliases' => [
//                    'ProductFieldset' => ProductFieldset::class,
//                    'Product' => ProductForm::class,
                ]
            ]
        ];
    }
    public function getTemplates(): array
    {
        return [
            'paths' => [
//                'recipe' => [__DIR__ . '/../templates/recipe']
            ]
        ];
    }
}
