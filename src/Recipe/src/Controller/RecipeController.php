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
use Zend\Diactoros\Response\JsonResponse;
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
            $this->messenger()->addSuccess('Recipe deleted successfully', 'recipe');
            return new RedirectResponse($this->url('recipe', ['action' => 'my-recipes']));
        }

        $this->messenger()->addError('Recipe could not be deleted', 'recipe');
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
            $this->messenger()->addError($this->forms()->getMessages($form));
            $this->forms()->saveState($form);
            return new RedirectResponse($request->getUri(), 303);
        }

        $products = [];
        $recipeProducts = $this->recipeProductService->getRecipeProducts($recipe->getId());
        /** @var RecipeProductEntity $recipeProduct */
        foreach ($recipeProducts as $recipeProduct) {
            /** @var ProductEntity $product */
            $product = $this->productService->getProduct($recipeProduct->getProductId());
            $product = $this->productService->calculateMacros($product, $recipeProduct->getQuantity());
            $products[] = $product;
        }
        $data = [
            'form' => $form,
            'recipeProducts' => $recipeProducts,
            'products' => $products,
            'recipe' => $recipe
        ];
        return new HtmlResponse($this->template('recipe::edit', $data));
    }

    public function editRecipeProductAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $recipeProductData = $request->getParsedBody();
            if (!isset($recipeProductData['recipeProductId']) ||
                !isset($recipeProductData['quantity']) ||
                !isset($recipeProductData['type'])
            ) {
                return new JsonResponse(json_encode([
                    'success' => 'false',
                    'info' => 'Missing data for RecipeProduct entity',
                ]));
            }

            /** @var RecipeProductEntity $recipeProduct */
            $recipeProduct = $this->recipeProductService->getRecipeProduct($recipeProductData['recipeProductId']);

            $type = $recipeProductData['type'];
            if ($type == "update") {
                $recipeProduct->setQuantity($recipeProductData['quantity']);
            } elseif ($type == "delete") {
                $recipeProduct->setStatus('deleted');
            }

            $success = $this->recipeProductService->save($recipeProduct);

            if ($success) {
                $this->messenger()->addSuccess('Recipe product ' . $type . 'd successfully', 'recipe');
            } else {
                $this->messenger()->addError('Failed to ' . $type . ' recipe product', 'recipe');
            }

            $jsonData = json_encode([
                'success' => $success,
                'recipeProductData' => $recipeProductData,
            ]);
            return new JsonResponse($jsonData);
        }

        return new JsonResponse(json_encode([
            'success' => 'false',
            'info' => 'No POST data received',
        ]));
    }

    public function addProductAction()
    {
        $request = $this->getRequest();
        $recipeId = $request->getAttribute('id');

        $form = $this->forms('Product');

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();
                $searchTerm = $data['product']['search'];
                $matchingProducts = $this->productService->searchProductsByTitle($searchTerm);
                $data = [
                    'form' => $form,
                    'products' => $matchingProducts,
                    'recipeId' => $recipeId,
                ];
                return new HtmlResponse($this->template('recipe::add-product', $data));
            }
            $this->messenger()->addError($this->forms()->getMessages($form));
            $this->forms()->saveState($form);
            return new RedirectResponse($request->getUri(), 303);
        }
        return new HtmlResponse($this->template('recipe::add-product', ['form' => $form]));
    }

    public function saveRecipeProductAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $recipeProductData = $request->getParsedBody();

            if (!isset($recipeProductData['recipeId']) ||
                !isset($recipeProductData['productId']) ||
                !isset($recipeProductData['quantity'])
            ) {
                return new JsonResponse(json_encode([
                    'success' => 'false',
                    'info' => 'Missing data for RecipeProduct entity',
                ]));
            }

            $recipeProduct = RecipeProductEntity::fromArray([
                'recipeId' => $recipeProductData['recipeId'],
                'productId' => $recipeProductData['productId'],
                'quantity' => $recipeProductData['quantity'],
            ]);

            $success = $this->recipeProductService->save($recipeProduct);

            if ($success) {
                $this->messenger()->addSuccess('Product added to recipe successfully', 'recipe');
            } else {
                $this->messenger()->addError('Failed to add product to recipe', 'recipe');
            }
            $jsonData = json_encode([
                'success' => $success,
                'recipeProductData' => $recipeProductData,
            ]);
            return new JsonResponse($jsonData);
        }

        return new JsonResponse(json_encode([
            'success' => 'false',
            'info' => 'No POST data received',
        ]));
    }

    public function saveMealToRecipeAction()
    {
        $request = $this->getRequest();
        $mealId = $request->getAttribute('id');

        $mealProducts = $this->mealProductService->getMealProducts($mealId);

        /** @var MealProductEntity $mealProduct */
        foreach ($mealProducts as $mealProduct) {
            $products[] = $this->productService->getProduct($mealProduct->getProductId());
            $quantities[] = $mealProduct->getQuantity();
        }

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
                        /** @var ProductEntity $product */
                        foreach ($products as $product) {
                            $recipeProduct = RecipeProductEntity::fromArray([
                                'recipeId' => $savedRecipe->getId(),
                                'productId' => $product->getId(),
                                'quantity' => $quantities[$product->getId()] ?? 0,
                            ]);
                            
                            $success = $this->recipeProductService->save($recipeProduct);
                        }
                        $this->messenger()->addSuccess('Recipe saved successfully!', 'recipe');
                        return new RedirectResponse($this->url('recipe', ['action' => 'my-recipes']));
                    }
                }
            }
        }

        $data = [
            'form' => $form,
            'products' => $products,
            'quantities' => $quantities,
        ];

        return new HtmlResponse($this->template('recipe::add', $data));
    }
}
