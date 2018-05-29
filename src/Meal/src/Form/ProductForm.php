<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal\Form;

use Zend\Form\Form;

class ProductForm extends Form
{
    /**
     * AccountForm constructor.
     */
    public function __construct()
    {
        parent::__construct('productForm');

        $this->setAttribute('method', 'post');
    }

    public function init()
    {
        parent::init();

        $this->setValidationGroup([
            'product' => [
                'search',
            ],
            // add submit to validation group,
            // not needed usually bu needed for the form display helper partial template
            'submit'
        ]);

        $this->add([
            'type'    => 'ProductFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Search Products'
            ]
        ], ['priority' => -100]);
    }
}
