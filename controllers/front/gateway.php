<?php

require_once dirname(__FILE__) . '/../../classes/Api/Router.php';
require_once dirname(__FILE__) . '/../../classes/Api/AbstractController.php';
require_once dirname(__FILE__) . '/../../classes/Api/Controllers/V1/OrderController.php';
require_once dirname(__FILE__) . '/../../classes/Api/Controllers/V2/OrderController.php';
require_once dirname(__FILE__) . '/../../classes/Auth/ApiClientProvider.php';
require_once dirname(__FILE__) . '/../../classes/Middleware/AuthMiddleware.php';
require_once dirname(__FILE__) . '/../../classes/Repository/OrderRepository.php';
require_once dirname(__FILE__) . '/../../classes/Response/ApiResponse.php';
require_once dirname(__FILE__) . '/../../classes/Service/OrderService.php';

use LimepackApi\Classes\Api\Router;
use LimepackApi\Classes\Api\Controllers\V1\OrderController as V1OrderController;
use LimepackApi\Classes\Api\Controllers\V2\OrderController as V2OrderController;
use LimepackApi\Classes\Auth\ApiClientProvider;
use LimepackApi\Classes\Middleware\AuthMiddleware;
use LimepackApi\Classes\Repository\OrderRepository;
use LimepackApi\Classes\Service\OrderService;

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

        $repository = new OrderRepository();

        $service = new OrderService(
            $repository
        );

        $v1Controller = new V1OrderController(
            $this->module,
            $service,
            $authMiddleware
        );

        $v2Controller = new V2OrderController(
            $this->module,
            $service,
            $authMiddleware
        );

        $router = new Router(
            $this->module,
            $v1Controller,
            $v2Controller
        );

        $router->dispatch();
    }
}
