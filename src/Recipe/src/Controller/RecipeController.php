<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe\Controller;

use Dot\Controller\AbstractActionController;
use Fig\Http\Message\RequestMethodInterface;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Tracker\Frontend\Product\Service\ProductService;
use Tracker\Frontend\Recipe\Service\RecipeService;
use Zend\Diactoros\Response\HtmlResponse;
use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;
use Zend\Session\Container;

/**
 * Class RecipeController
 * @package Tracker\Frontend\Recipe\Controller
 *
 * @method UrlHelperPlugin|UriInterface url($r = null, $p = [], $q = [], $f = null, $o = [])
 * @method FlashMessengerPlugin messenger()
 * @method FormsPlugin|Form forms(string $name = null)
 * @method TemplatePlugin|string template(string $template = null, array $params = [])
 * @method AuthenticationPlugin authentication()
 * @method AuthorizationPlugin isGranted(string $permission, array $roles = [], $context = null)
 * @method Container session(string $namespace)
 *
 * @Service
 */
class RecipeController extends AbstractActionController
{
    /** @var  RecipeService */
    protected $recipeService;

    /** @var  ProductService */
    protected $productService;

    /**
     * RecipeController constructor.
     * @param RecipeService  $recipeService
     * @param ProductService $productService
     * @Inject ({RecipeService::class, ProductService::class})
     */
    public function __construct(RecipeService $recipeService, ProductService $productService)
    {
        $this->recipeService = $recipeService;
        $this->productService = $productService;
    }

    public function testAction()
    {
        $recipe = $this->recipeService->getUserRecipes(1);
        $products = $this->productService->getRecipeProducts($recipe[0]);
        return new HtmlResponse('Great success!');
    }
}
