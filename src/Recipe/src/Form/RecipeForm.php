<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class RecipeForm
 * @package Tracker\Frontend\Recipe\Form
 */
class RecipeForm extends Form
{
    protected $validationGroup = [
        'recipe' => [
            'name',
        ],
        'submit'
    ];

    /**
     * ProductForm constructor.
     */
    public function __construct()
    {
        parent::__construct('recipeForm');

        $this->setAttribute('method', 'post');
        $this->setInputFilter(new InputFilter());
    }

    public function init()
    {
        $this->add([
            'type' => 'RecipeFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit'
            ]
        ], ['priority' => -100]);

        $this->setValidationGroup($this->validationGroup);
    }
}
