<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Tracker\Frontend\Product;

use Dot\Mapper\Factory\DbMapperFactory;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Tracker\Frontend\Product\Form\ProductFieldset;
use Tracker\Frontend\Product\Form\ProductSearchFieldset;
use Tracker\Frontend\Product\Form\ProductSearchForm;
use Tracker\Frontend\Product\Form\RecipeFieldset;
use Tracker\Frontend\Product\Form\ProductForm;
use Tracker\Frontend\Product\Mapper\ProductDbMapper;
use Tracker\Frontend\Product\Service\ProductService;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package Tracker\Frontend\Product
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
                ProductService::class => InvokableFactory::class,
            ],
            'aliases' => [
                'ProductService' => ProductService::class,
            ]
        ];
    }

    public function getMappers()
    {
        return [
            'mapper_manager' => [
                'factories' => [
                    ProductDbMapper::class => DbMapperFactory::class,
                ],
                'aliases' => [
                    ProductEntity::class => ProductDbMapper::class,
                ]
            ],
        ];
    }

    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories' => [
                    ProductForm::class    => InvokableFactory::class,
                    ProductSearchForm::class => InvokableFactory::class,
                    ProductFieldset::class => InvokableFactory::class,
                    ProductSearchFieldset::class => InvokableFactory::class
                ],
                'aliases' => [
                    'Product' => ProductForm::class,
                    'ProductSearch' => ProductSearchForm::class,
                    'ProductFieldset' => ProductFieldset::class,
                    'ProductSearchFieldset' => ProductSearchFieldset::class,
                ]
            ]
        ];
    }
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'product' => [__DIR__ . '/../templates/product']
            ]
        ];
    }
}
