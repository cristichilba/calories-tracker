<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Product\Form;

use Dot\Hydrator\ClassMethodsCamelCase;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class ProductFieldset
 *
 * @package Tracker\Frontend\Product\Form
 */
class ProductFieldset extends Fieldset implements InputFilterProviderInterface
{
    protected $hydrator;
    protected $object;

    /**
     * ProductFieldset constructor.
     */
    public function __construct()
    {
        parent::__construct('product');
        $this->setHydrator(new ClassMethodsCamelCase());
        $this->setObject(ProductEntity::emptyProduct());
    }

    public function init()
    {
        $this->add([
            'name'       => 'title',
            'type'       => 'text',
            'options'    => [
                'label' => 'Title',
            ],
            'attributes' => [
                'id'          => 'title',
                'placeholder' => 'Product title...',
                'required' => 'required',
            ]
        ]);
        $this->add([
            'name' => 'carbs',
            'type' => 'number',
            'options' => [
                'label' => 'Carbohydrates',
            ],
            'attributes' => [
                'id' => 'carbs',
                'value' => '0',
                'step' => '0.1'
            ],
        ]);

        $this->add([
            'name' => 'protein',
            'type' => 'number',
            'options' => [
                'label' => 'Protein',
            ],
            'attributes' => [
                'id' => 'protein',
                'value' => '0',
                'step' => '0.1'
            ],
        ]);
        $this->add([
            'name' => 'fat',
            'type' => 'number',
            'options' => [
                'label' => 'Fat',
            ],
            'attributes' => [
                'id' => 'fat',
                'value' => '0',
                'step' => '0.5'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'title' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Title</b> is required and cannot be empty',
                        ]
                    ],
                ]
            ],
            'carbs' => [
                'validators' => [
                    [
                        'name' => 'Float',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Carbohydrates</b> must have a float value',
                        ]
                    ],
                    [
                        'name' => 'GreaterThan',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => 0,
                            'inclusive' => true,
                            'message' => '<b>Carbohydrates</b> cannot be negative',
                        ]
                    ],
                ]
            ],
            'protein' => [
                'validators' => [
                    [
                        'name' => 'Float',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Protein</b> must have a float value',
                        ]
                    ],
                    [
                        'name' => 'GreaterThan',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => 0,
                            'inclusive' => true,
                            'message' => '<b>Protein</b> cannot be negative',
                        ]
                    ],
                ]
            ],
            'fat' => [
                'validators' => [
                    [
                        'name' => 'Float',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Fat</b> must have a float value',
                        ]
                    ],
                    [
                        'name' => 'GreaterThan',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => 0,
                            'inclusive' => true,
                            'message' => '<b>Fat</b> cannot be negative',
                        ]
                    ],
                ]
            ],
        ];
    }
}
