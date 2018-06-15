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
class ProductSearchFieldset extends Fieldset implements InputFilterProviderInterface
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
    }

    public function init()
    {
        $this->add([
            'name'       => 'search',
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
    }

    public function getInputFilterSpecification()
    {
        return [
            'search' => [
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
        ];
    }
}
