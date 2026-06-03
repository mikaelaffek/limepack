<?php

namespace LimepackApi\Classes\Api;

use LimepackApi\Classes\Api\Controllers\V1\OrderController as V1OrderController;
use LimepackApi\Classes\Api\Controllers\V2\OrderController as V2OrderController;

class Router
{
    protected $module;

    protected $v1OrderController;

    protected $v2OrderController;

    public function __construct(
        \Module $module,
        V1OrderController $v1OrderController,
        V2OrderController $v2OrderController
    ) {
        $this->module = $module;

        $this->v1OrderController = $v1OrderController;

        $this->v2OrderController = $v2OrderController;
    }

    public function dispatch()
    {
        $version = \Tools::getValue('version');

        $resource = \Tools::getValue('resource');

        $context = array(
            'id' => \Tools::getValue('id'),

            'pagination' => array(
                'limit' => min(
                    (int)\Tools::getValue('limit', 20),
                    100
                ),

                'offset' => max(
                    (int)\Tools::getValue('offset', 0),
                    0
                ),
            ),
        );

        switch ($version) {

            case 'v1':

                $controller = $this->v1OrderController;

                break;

            case 'v2':

                $controller = $this->v2OrderController;

                break;

            default:

                throw new \Exception(
                    'API version not found'
                );
        }

        if ($resource !== 'orders') {
            throw new \Exception(
                'Resource not found'
            );
        }

        $controller->setContext($context);

        $controller->handle();
    }
}
