<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Product\Controller;

use Dot\Controller\AbstractActionController;
use Fig\Http\Message\RequestMethodInterface;
use Tracker\Frontend\Product\Entity\ProductEntity;
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
use Zend\Form\Form;
use Zend\Session\Container;

/**
 * Class ProductController
 * @package Tracker\Frontend\Product\Controller
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
class ProductController extends AbstractActionController
{
    protected $productService;

    /**
     * ProductController constructor.
     * @Inject({ProductService::class})
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function submitAction()
    {
        $request = $this->getRequest();
        $form = $this->forms('Product');

        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();

            $form->setData($data);

            if ($form->isValid()) {
                $product = ProductEntity::fromArray($data['product']);
                $success = $this->productService->save($product);
                if ($success) {
                    $this->messenger()->addSuccess(
                        'Product submitted successfuly! It will be reviewed by an administrator.',
                        'product'
                    );
                    return new RedirectResponse($request->getUri());
                }
            }
        }

        $data = [
            'form' => $form
        ];

        return new HtmlResponse($this->template("product::product-submit", $data));
    }
}
