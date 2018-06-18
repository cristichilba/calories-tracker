<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source
 *      repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT
 *          License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal\Controller;

use Dot\Controller\AbstractActionController;
use Fig\Http\Message\RequestMethodInterface;
use Tracker\Frontend\Meal\Entity\MealEntity;
use Tracker\Frontend\Meal\Entity\MealProductEntity;
use Tracker\Frontend\Meal\Entity\MealRecipeEntity;
use Tracker\Frontend\Meal\Service\MealRecipeService;
use Tracker\Frontend\Meal\Service\MealService;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Tracker\Frontend\Product\Service\ProductService;
use Tracker\Frontend\Meal\Service\MealProductService;
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
 * Class MealController
 * @package Tracker\Frontend\Meal\Controller
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
class MealController extends AbstractActionController
{

    /** @var  ProductService */
    protected $productService;
    /** @var RecipeService */
    protected $recipeService;
    /** @var  MealService */
    protected $mealService;
    /** @var MealProductService  */
    protected $mealProductService;
    /** @var MealRecipeService */
    protected $mealRecipeService;
    /** @var RecipeProductService */
    protected $recipeProductService;

    /**
     * MealController constructor.
     * @param ProductService $productService
     * @param RecipeService $recipeService
     * @param MealService $mealService
     * @param MealProductService $mealProductService
     * @param MealRecipeService $mealRecipeService
     * @param RecipeProductService $recipeProductService
     *
     * @Inject ({ ProductService::class, RecipeService::class, MealService::class,
     *     MealProductService::class, MealRecipeService::class, RecipeProductService::class})
     */
    public function __construct(
        ProductService $productService,
        RecipeService $recipeService,
        MealService $mealService,
        MealProductService $mealProductService,
        MealRecipeService $mealRecipeService,
        RecipeProductService $recipeProductService
    ) {
        $this->productService = $productService;
        $this->recipeService = $recipeService;
        $this->mealService = $mealService;
        $this->mealProductService = $mealProductService;
        $this->mealRecipeService = $mealRecipeService;
        $this->recipeProductService = $recipeProductService;
    }

    public function viewAction()
    {
        $request = $this->getRequest();
        $date = $request->getAttribute('date');
        if (!isset($date)) {
            $currentDate = (new \DateTime());
            $date = $currentDate->format('Y-m-d');
        } else {
            $currentDate = (new \DateTime($date));
        }

        $meals = [
            'breakfast' => $this->mealService->getMealOnDateByType($currentDate, 'breakfast'),
            'lunch' => $this->mealService->getMealOnDateByType($currentDate, 'lunch'),
            'dinner' => $this->mealService->getMealOnDateByType($currentDate, 'dinner'),
            'snacks' => $this->mealService->getMealOnDateByType($currentDate, 'snacks'),
        ];

        foreach ($meals as $type => $meal) {
            if ($meal instanceof MealEntity) {
                $mealProducts = $this->mealProductService->getMealProducts($meal->getId());
                $mealRecipes = $this->mealRecipeService->getMealRecipes($meal->getId());

                /** @var MealRecipeEntity $mealRecipe */
                foreach ($mealRecipes as $mealRecipe) {
                    /** @var RecipeEntity $recipe */
                    $recipe = $this->recipeService->getRecipe($mealRecipe->getRecipeId());
                    $meals[$type . 'Recipes'][$recipe->getId()] = $recipe;
    
                    $recipeProducts = $this->recipeProductService->getRecipeProducts($recipe->getId());
                    /** @var RecipeProductEntity $recipeProduct */
                    foreach ($recipeProducts as $recipeProduct) {
                        /** @var ProductEntity $product */
                        $product = $this->productService->getProduct($recipeProduct->getProductId());
                        $product = $this->productService->calculateMacros($product, $recipeProduct->getQuantity());

                        $meals[$type . 'RecipeProducts'][$recipe->getId()][] = $product;
                        $meals[$type . 'RecipeRecipeProducts'][$recipe->getId()][] = $recipeProduct;
                    }
                }

                /** @var MealProductEntity $mealProduct */
                foreach ($mealProducts as $mealProduct) {
                    /** @var ProductEntity $product */
                    $product = $this->productService->getProduct($mealProduct->getProductId());
                    $product = $this->productService->calculateMacros($product, $mealProduct->getQuantity());
                    $meals[$type . 'Products'][] = $product;
                    $meals[$type . 'MealProducts'][] = $mealProduct;
                }
            }
        }
        
        $data = [
            'currentDay' => $date,
            'previousDay' => $currentDate->modify('-1 day')->format('Y-m-d'),
            'nextDay' => $currentDate->modify('+2 day')->format('Y-m-d'),
            'meals' => $meals,
        ];
        
        return new HtmlResponse($this->template('meal::view', $data));
    }

    public function addProductAction()
    {
        $request = $this->getRequest();

        $type = $request->getAttribute('type');
        $date = $request->getAttribute('date');
        if ($date === 'today') {
            $date = (new \DateTime("now"))->format('Y-m-d');
        }

        $form = $this->forms('ProductSearch');

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
                    'date' => $date,
                    'type' => $type,
                    'userId' => $this->authentication()->getIdentity()->getId()
                ];
                return new HtmlResponse($this->template('meal::add-product', $data));
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }
        $data = [
            'form' => $form,
            'date' => $date,
            'type' => $type
        ];
        return new HtmlResponse($this->template('meal::add-product', $data));
    }

    public function addRecipeAction()
    {
        $request = $this->getRequest();

        $type = $request->getAttribute('type');
        $date = $request->getAttribute('date');
        if ($date === 'today') {
            $date = (new \DateTime("now"))->format('Y-m-d');
        }

        $form = $this->forms('Recipe');

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();
            $form->setData($data);

            if ($form->isValid()) {
                $data = [
                    'form' => $form,
                    'date' => $date,
                    'type' => $type,
                    'userId' => $this->authentication()->getIdentity()->getId()
                ];

                /** @var RecipeEntity $data */
                $recipeData = $form->getData();
                $searchTerm = $recipeData->getName();

                $matchingRecipes = $this->recipeService->searchRecipesByTitle($searchTerm);
                $data['recipes'] = $matchingRecipes;

                /** @var RecipeEntity $recipe */
                foreach ($matchingRecipes as $recipe) {
                    $recipeProducts = $this->recipeProductService->getRecipeProducts($recipe->getId());
                    $data['recipeProducts'][$recipe->getId()] = $recipeProducts;

                    /** @var RecipeProductEntity $recipeProduct */
                    foreach ($recipeProducts as $recipeProduct) {
                        /** @var ProductEntity $product */
                        $product = $this->productService->getProduct($recipeProduct->getProductId());
                        $product = $this->productService->calculateMacros($product, $recipeProduct->getQuantity());
                        $data['products'][$recipe->getId()][] = $product;
                    }
                }

                return new HtmlResponse($this->template('meal::add-recipe', $data));
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }
        $data = [
            'form' => $form,
            'date' => $date,
            'type' => $type
        ];
        return new HtmlResponse($this->template('meal::add-recipe', $data));
    }

    public function saveMealProductAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $mealData = $request->getParsedBody();

            if (!isset($mealData['userId']) ||
                !isset($mealData['type'])||
                !isset($mealData['date']) ||
                !isset($mealData['productId']) ||
                !isset($mealData['quantity'])
            ) {
                return new JsonResponse(json_encode([
                    'success' => 'false',
                    'info' => 'Missing data for meal entity',
                ]));
            }
            /** @var ProductEntity $product */
            $product = $this->productService->getProduct($mealData['productId']);
            $quantity = $mealData['quantity'];

            /** @var MealEntity $existingMeal */
            $existingMeal = $this->mealService->getMealOnDateByType($mealData['date'], $mealData['type']);
            if (!$existingMeal) {
                // first we create a meal entity and save it
                $meal = MealEntity::fromArray($mealData);
                $savedMeal = $this->mealService->save($meal);

                $mealProduct = MealProductEntity::fromArray([
                    'mealId' => $savedMeal->getId(),
                    'productId' => $mealData['productId'],
                    'quantity' => $quantity,
                ]);
            } else {
                $mealProduct = MealProductEntity::fromArray([
                    'mealId' => $existingMeal->getId(),
                    'productId' => $mealData['productId'],
                    'quantity' => $mealData['quantity'],
                ]);
            }
            $success = $this->mealProductService->save($mealProduct);

            $jsonData = json_encode([
                'success' => $success,
                'mealData' => $mealData,
            ]);

            return new JsonResponse($jsonData);
        }

        return new JsonResponse(json_encode([
            'success' => 'false',
            'info' => 'No POST data received',
        ]));
    }

    public function saveMealRecipeAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $mealData = $request->getParsedBody();

            if (!isset($mealData['userId']) ||
                !isset($mealData['type'])||
                !isset($mealData['date']) ||
                !isset($mealData['recipeId'])
            ) {
                return new JsonResponse(json_encode([
                    'success' => 'false',
                    'info' => 'Missing data for meal entity',
                ]));
            }
            /** @var ProductEntity $product */
            $recipe = $this->recipeService->getRecipe($mealData['recipeId']);

            /** @var MealEntity $existingMeal */
            $existingMeal = $this->mealService->getMealOnDateByType($mealData['date'], $mealData['type']);
            
            if (!$existingMeal) {
                // first we create a meal entity and save it
                $meal = MealEntity::fromArray($mealData);
                $savedMeal = $this->mealService->save($meal);

                /** @var MRE $mealRecipe */
                $mealRecipe = MealRecipeEntity::fromArray([
                    'mealId' => $savedMeal->getId(),
                    'recipeId' => $mealData['recipeId'],
                ]);
            } else {
                /** @var MealRecipeEntity $mealRecipe */
                $mealRecipe = MealRecipeEntity::fromArray([
                    'mealId' => $existingMeal->getId(),
                    'recipeId' => $mealData['recipeId'],
                ]);
            }

            $success = $this->mealRecipeService->save($mealRecipe);

            $jsonData = json_encode([
                'success' => $success,
                'mealData' => $mealData,
            ]);

            return new JsonResponse($jsonData);
        }

        return new JsonResponse(json_encode([
            'success' => 'false',
            'info' => 'No POST data received',
        ]));
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $date = $request->getAttribute('date');
        $type = $request->getAttribute('type');

        /** @var MealEntity $meal */
        $meal = $this->mealService->getMealOnDateByType($date, $type);

        $mealProducts = $this->mealProductService->getMealProducts($meal->getId());
        $mealRecipes = $this->mealRecipeService->getMealRecipes($meal->getId());

        /** @var MealRecipeEntity $mealRecipe */
        foreach ($mealRecipes as $mealRecipe) {
            /** @var RecipeEntity $recipe */
            $recipe = $this->recipeService->getRecipe($mealRecipe->getRecipeId());
            $recipes['recipes'][] = $recipe;

            $recipeProducts = $this->recipeProductService->getRecipeProducts($mealRecipe->getRecipeId());

            /** @var RecipeProductEntity $recipeProduct */
            foreach ($recipeProducts as $recipeProduct) {
                /** @var ProductEntity $product */
                $product = $this->productService->getProduct($recipeProduct->getProductId());
                $product = $this->productService->calculateMacros($product, $recipeProduct->getQuantity());

                $recipes['products'][$recipe->getId()][] = $product;
                $recipes['recipeProducts'][$recipe->getId()][] = $recipeProduct;
            }
        }

        /** @var MealProductEntity $mealProduct */
        foreach ($mealProducts as $mealProduct) {
            /** @var ProductEntity $product */
            $product = $this->productService->getProduct($mealProduct->getProductId());
            $product = $this->productService->calculateMacros($product, $mealProduct->getQuantity());
            $products[] = $product;
        }

        $data = [
            'type' => $type,
            'meal' => $meal,
            'mealProducts' => $mealProducts,
            'mealRecipes' => $mealRecipes,
            'recipeData' => $recipes,
            'products' => $products,
        ];

        return new HtmlResponse($this->template("meal::edit", $data));
    }

    public function editMealProductAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $mealProductData = $request->getParsedBody();
            if (!isset($mealProductData['mealProductId']) ||
                !isset($mealProductData['quantity']) ||
                !isset($mealProductData['type'])
            ) {
                return new JsonResponse(json_encode([
                    'success' => 'false',
                    'info' => 'Missing data for MealProduct entity',
                ]));
            }

            /** @var MealProductEntity $mealProduct */
            $mealProduct = $this->mealProductService->getMealProduct($mealProductData['mealProductId']);

            $type = $mealProductData['type'];
            if ($type == "update") {
                $mealProduct->setQuantity($mealProductData['quantity']);
            } elseif ($type == "delete") {
                $mealProduct->setStatus('deleted');
            }

            $success = $this->mealProductService->save($mealProduct);

            if ($success) {
                $this->messenger()->addSuccess('Meal product ' . $type . 'd successfully', 'meals');
            } else {
                $this->messenger()->addError('Failed to ' . $type . ' meal product', 'meals');
            }

            $jsonData = json_encode([
                'success' => $success,
                'mealProductData' => $mealProductData,
            ]);
            return new JsonResponse($jsonData);
        }

        return new JsonResponse(json_encode([
            'success' => 'false',
            'info' => 'No POST data received',
        ]));
    }

    public function editMealRecipeAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $mealRecipeData = $request->getParsedBody();
            if (!isset($mealRecipeData['mealRecipeId']) ||
                !isset($mealRecipeData['type'])
            ) {
                return new JsonResponse(json_encode([
                    'success' => 'false',
                    'info' => 'Missing data for MealRecipe entity',
                ]));
            }

            $type = $mealRecipeData['type'];

            /** @var MealRecipeEntity $mealRecipe */
            $mealRecipe = $this->mealRecipeService->getMealRecipe($mealRecipeData['mealRecipeId']);
            if ($type == "delete") {
                $mealRecipe->setStatus('deleted');
            }

            $success = $this->mealRecipeService->save($mealRecipe);

            if ($success) {
                $this->messenger()->addSuccess('Meal recipe ' . $type . 'd successfully', 'meals');
            } else {
                $this->messenger()->addError('Failed to ' . $type . ' meal recipe', 'meals');
            }

            $jsonData = json_encode([
                'success' => $success,
                'mealRecipeData' => $mealRecipeData,
            ]);
            return new JsonResponse($jsonData);
        }

        return new JsonResponse(json_encode([
            'success' => 'false',
            'info' => 'No POST data received',
        ]));
    }
}
