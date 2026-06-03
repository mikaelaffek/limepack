<?php

require_once dirname(__FILE__) . '/../../classes/Exception/NotFoundException.php';
require_once dirname(__FILE__) . '/../../classes/Api/Router.php';
require_once dirname(__FILE__) . '/../../classes/Api/AbstractController.php';
require_once dirname(__FILE__) . '/../../classes/Api/Controllers/V1/OrderController.php';
require_once dirname(__FILE__) . '/../../classes/Api/Controllers/V1/TrackingController.php';
require_once dirname(__FILE__) . '/../../classes/Api/Controllers/V2/OrderController.php';
require_once dirname(__FILE__) . '/../../classes/Auth/ApiClientProvider.php';
require_once dirname(__FILE__) . '/../../classes/Middleware/AuthMiddleware.php';
require_once dirname(__FILE__) . '/../../classes/Repository/OrderRepository.php';
require_once dirname(__FILE__) . '/../../classes/Response/ApiResponse.php';
require_once dirname(__FILE__) . '/../../classes/Service/OrderService.php';
require_once dirname(__FILE__) . '/../../classes/Service/TrackingService.php';

use LimepackApi\Classes\Api\Router;
use LimepackApi\Classes\Api\Controllers\V1\OrderController as V1OrderController;
use LimepackApi\Classes\Api\Controllers\V1\TrackingController as V1TrackingController;
use LimepackApi\Classes\Api\Controllers\V2\OrderController as V2OrderController;
use LimepackApi\Classes\Auth\ApiClientProvider;
use LimepackApi\Classes\Middleware\AuthMiddleware;
use LimepackApi\Classes\Repository\OrderRepository;
use LimepackApi\Classes\Service\OrderService;
use LimepackApi\Classes\Service\TrackingService;

class LimepackApiGatewayModuleFrontController
    extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $provider = new ApiClientProvider();

        $authMiddleware = new AuthMiddleware(
            $provider
        );

        $orderService = new OrderService(
            new OrderRepository()
        );

        $trackingService = new TrackingService();

        $controllers = array(

            'v1' => array(

                'orders' => new V1OrderController(
                    $this->module,
                    $orderService,
                    $authMiddleware
                ),

                'tracking' => new V1TrackingController(
                    $this->module,
                    $trackingService,
                    $authMiddleware
                ),
            ),

            'v2' => array(

                'orders' => new V2OrderController(
                    $this->module,
                    $orderService,
                    $authMiddleware
                ),
            ),
        );

        $router = new Router(
            $this->module,
            $controllers
        );

        $router->dispatch();
    }
}
