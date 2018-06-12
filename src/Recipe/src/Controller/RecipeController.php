<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source
 *      repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT
 *          License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe\Controller;

use Dot\Controller\AbstractActionController;
use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tracker\Frontend\Meal\Entity\MealProductEntity;
use Tracker\Frontend\Meal\Service\MealProductService;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Tracker\Frontend\Product\Service\ProductService;
use Tracker\Frontend\Recipe\Entity\RecipeEntity;
use Tracker\Frontend\Recipe\Entity\RecipeProductEntity;
use Tracker\Frontend\Recipe\Service\RecipeProductService;
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
    /** @var RecipeProductService  */
    protected $recipeProductService;
    /** @var  MealProductService */
    protected $mealProductService;

    /**
     * RecipeController constructor.
     * @param RecipeService  $recipeService
     * @param ProductService $productService
     * @param RecipeProductService $recipeProductService
     * @param MealProductService $mealProductService
     * @Inject ({RecipeService::class, ProductService::class,
     *         RecipeProductService::class, MealProductService::class})
     */
    public function __construct(
        RecipeService $recipeService,
        ProductService $productService,
        RecipeProductService $recipeProductService,
        MealProductService $mealProductService
    ) {
        $this->recipeService = $recipeService;
        $this->productService = $productService;
        $this->recipeProductService = $recipeProductService;
        $this->mealProductService = $mealProductService;
    }

    public function myRecipesAction()
    {
        if (!$this->authentication()->hasIdentity()) {
            $this->messenger()->addError('You must sign in first in order to access the requested content.');
            return new RedirectResponse($this->url('user', ['action' => 'login']));
        }

        $userId = $this->authentication()->getIdentity()->getId();
        $userRecipes = $this->recipeService->getUserRecipes($userId);

        $data = [];
        /** @var RecipeEntity $recipe */
        foreach ($userRecipes as $recipe) {
            $data['recipes'][$recipe->getId()] = $recipe;
            $recipeProducts = $this->recipeProductService->getRecipeProducts($recipe->getId());
            $data['recipeProducts'][$recipe->getId()] = $recipeProducts;

            /** @var RecipeProductEntity $recipeProduct */
            foreach ($recipeProducts as $recipeProduct) {
                $product = $this->productService->getProduct($recipeProduct->getProductId());
                // calculate product values based on quantity
                $product = $this->productService->calculateMacros($product, $recipeProduct->getQuantity());
                $data['products'][$recipe->getId()][] = $product;
            }
        }
        return new HtmlResponse($this->template('recipe::my-recipes', $data));
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $recipeId = $request->getAttribute('id');

        /** @var RecipeEntity $recipe */
        $recipe = $this->recipeService->getRecipe($recipeId);
        $recipe->setStatus('inactive');
        $success = $this->recipeService->save($recipe);

        if ($success instanceof RecipeEntity) {
            $this->messenger()->addSuccess('Recipe deleted successfully!', 'recipe');
            return new RedirectResponse($this->url('recipe', ['action' => 'my-recipes']));
        }

        $this->messenger()->addError('Recipe could not be deleted.', 'recipe');
        return new RedirectResponse($this->url('recipe', ['action' => 'my-recipes']));
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $recipeId = $request->getAttribute('id');

        /** @var RecipeEntity $recipe */
        $recipe = $this->recipeService->getRecipe($recipeId);
        $form = $this->forms('Recipe');
        $form->bind($recipe);
        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();
            $form->setData($data);

            if ($form->isValid()) {
                $recipe = $form->getData();
                $success = $this->recipeService->save($recipe);

                if ($success instanceof RecipeEntity) {
                    $this->messenger()->addSuccess('Recipe updated successfully!', 'recipe');
                    return new RedirectResponse($this->url('recipe', ['action' => 'my-recipes']));
                }

                $this->messenger()->addError('Recipe could not be updated.', 'recipe');
                return new RedirectResponse($this->url('recipe', ['action' => 'my-recipes']));
            }
        }
        return new HtmlResponse($this->template('recipe::edit', ['form' => $form]));
    }
    
    public function saveMealToRecipeAction()
    {
        $request = $this->getRequest();
        $mealId = $request->getAttribute('id');

        $mealProducts = $this->mealProductService->getMealProducts($mealId);
        $products = [];
        $quantities = [];
        /** @var MealProductEntity $mealProduct */
        foreach ($mealProducts as $mealProduct) {
            $products[] = $this->productService->getProduct($mealProduct->getProductId());
            $quantities[$mealProduct->getProductId()] = $mealProduct->getQuantity();
        }

        // @TODO: display products to be saved
        $form = $this->forms('Recipe');

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();
            $form->setData($data);

            if ($form->isValid()) {
                /** @var RecipeEntity $recipe */
                $recipe  = $form->getData();
                $userId = $this->authentication()->getIdentity()->getId();

                if ($recipe instanceof RecipeEntity) {
                    $recipe->setUserId($userId);
                    $savedRecipe = $this->recipeService->save($recipe);

                    if ($savedRecipe instanceof RecipeEntity) {
                        foreach ($products as $product) {
                            $recipeProduct = RecipeProductEntity::fromArray([
                                'recipeId' => $savedRecipe->getId(),
                                'productId' => $product->getId(),
                                'quantity' => $quantities[$product->getId()] ?? 0,
                            ]);
                            
                            $success = $this->recipeProductService->save($recipeProduct);
                        }
                        $this->messenger()->addSuccess('Recipe saved successfully!', 'recipe');
                    }
                }
            }
        }
        return new HtmlResponse($this->template('recipe::add', ['form' => $form]));
    }
}
