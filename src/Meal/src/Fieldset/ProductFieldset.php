<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal\Fieldset;

use Dot\Hydrator\ClassMethodsCamelCase;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class ProductFieldset extends Fieldset implements InputFilterProviderInterface
{
    const MESSAGE_PRODUCT_EMPTY = '<b>Product</b> is required and cannot be empty';
    const MESSAGE_PRODUCT_LIMIT = '<b>Product</b> character limit of 150 exceeded';
    /**
     * UserDetailsFieldset constructor.
     */
    public function __construct()
    {
        parent::__construct('product');
    }
    public function init()
    {
        $this->add([
            'name'       => 'search',
            'type'       => 'text',
            'options'    => [
                'label' => 'Search Products'
            ],
            'attributes' => [
                'placeholder' => 'Search Products...'
            ]
        ]);
    }
    public function getInputFilterSpecification(): array
    {
        return [
            'product' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => static::MESSAGE_PRODUCT_EMPTY
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 150,
                            'message' => static::MESSAGE_PRODUCT_LIMIT,
                        ],
                    ]
                ]
            ],
        ];
    }
}
