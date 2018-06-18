<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe\Form;

use Dot\Hydrator\ClassMethodsCamelCase;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Tracker\Frontend\Recipe\Entity\RecipeEntity;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class RecipeFieldset
 *
 * @package Tracker\Frontend\Recipe\Form
 */
class RecipeFieldset extends Fieldset implements InputFilterProviderInterface
{
    protected $hydrator;
    protected $object;

    /**
     * RecipeFieldset constructor.
     */
    public function __construct()
    {
        parent::__construct('recipe');
        $this->setHydrator(new ClassMethodsCamelCase());
        $this->setObject(RecipeEntity::emptyRecipe());
    }

    public function init()
    {
        $this->add([
            'name'       => 'name',
            'type'       => 'text',
            'options'    => [
                'label' => 'Name',
            ],
            'attributes' => [
                'id'          => 'name',
                'placeholder' => 'Recipe name...',
                'required' => 'required',
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => '<b>Name</b> is required and cannot be empty',
                        ]
                    ],
                ]
            ],
        ];
    }
}
