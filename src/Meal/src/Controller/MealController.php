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
use Tracker\Frontend\Meal\Service\MealService;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Tracker\Frontend\Product\Service\ProductService;
use Tracker\Frontend\Meal\Service\MealProductService;
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
    /** @var  MealService */
    protected $mealService;

    /** @var  ProductService */
    protected $productService;

    /** @var MealProductService  */
    protected $mealProductService;

    /**
     * MealController constructor.
     * @param MealService $mealService
     * @param ProductService $productService
     * @param MealProductService $mealProductService
     * @Inject ({MealService::class, ProductService::class,
     *         MealProductService::class})
     */
    public function __construct(
        MealService $mealService,
        ProductService $productService,
        MealProductService $mealProductService
    ) {
        $this->mealService = $mealService;
        $this->productService = $productService;
        $this->mealProductService = $mealProductService;
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
                /** @var MealProductEntity $mealProduct */
                foreach ($mealProducts as $mealProduct) {
                    /** @var ProductEntity $product */
                    $product = $this->productService->getProduct($mealProduct->getProductId());
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

    public function addMealAction()
    {
        $request = $this->getRequest();

        $type = $request->getAttribute('type');
        $date = $request->getAttribute('date');
        if ($date === 'today') {
            $date = (new \DateTime("now"))->format('Y-m-d');
        }

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
                    'carbs' => $quantity / 100 * $product->getCarbs(),
                    'protein' => $quantity / 100 * $product->getProtein(),
                    'fat' => $quantity / 100 * $product->getFat(),
                ]);
            } else {
                $mealProduct = MealProductEntity::fromArray([
                    'mealId' => $existingMeal->getId(),
                    'productId' => $mealData['productId'],
                    'quantity' => $mealData['quantity'],
                    'carbs' => $quantity / 100 * $product->getCarbs(),
                    'protein' => $quantity / 100 * $product->getProtein(),
                    'fat' => $quantity / 100 * $product->getFat()
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

    public function testAction()
    {

        // first we create a meal entity and save it
        $meal = MealEntity::fromArray([
            'userId' => 1,
            'date' => '2018-05-31',
            'type' => 'breakfast',
        ]);
        $test = $this->mealService->save($meal);

        return new HtmlResponse('a');
    }
}
