<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal\Controller;

use Dot\Controller\AbstractActionController;
use Fig\Http\Message\RequestMethodInterface;
use Tracker\Frontend\Meal\Service\MealService;
use Tracker\Frontend\Product\Service\ProductService;
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
use Zend\Form\Element\DateTime;
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

    /**
     * MealController constructor.
     * @param MealService $mealService
     * @Inject ({MealService::class, ProductService::class})
     */
    public function __construct(MealService $mealService, ProductService $productService)
    {
        $this->mealService = $mealService;
        $this->productService = $productService;
    }

    public function viewAction()
    {
        $request = $this->getRequest();
        $date = $request->getAttribute('date');

        if (!isset($date)) {
            $currentDate = (new \DateTime());
        } else {
            $currentDate = (new \DateTime($date));
        }
        
        $meals = [
            'breakfast' => $this->mealService->getMealsOnDateByType($currentDate, 'breakfast'),
            'lunch' => $this->mealService->getMealsOnDateByType($currentDate, 'lunch'),
            'dinner' => $this->mealService->getMealsOnDateByType($currentDate, 'dinner'),
            'snacks' => $this->mealService->getMealsOnDateByType($currentDate, 'snacks'),
        ];

        $data = [
            'currentDay' => $date,
            'previousDay' => $currentDate->modify('-1 day')->format('Y-m-d'),
            'nextDay' => $currentDate->modify('+2 day')->format('Y-m-d'),
        ];

        return new HtmlResponse($this->template('meal::view', $data));
    }

    public function addBreakfastAction()
    {
        $request = $this->getRequest();
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
                    'products' => $matchingProducts
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
        ];
        return new HtmlResponse($this->template('meal::add-product', $data));
    }
}
