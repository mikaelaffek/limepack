<?php

namespace LimepackApi\Classes\Api;

/**
 * Dispatches an incoming API request to the correct versioned controller.
 *
 * Controllers are supplied as a nested map keyed by version then resource:
 *   ['v1' => ['orders' => $ctrl, 'tracking' => $ctrl], 'v2' => [...]]
 *
 * Adding a new version or resource is just a matter of registering another
 * controller in that map (built in the gateway front controller).
 */
class Router
{
    /**
     * @var \Module
     */
    protected $module;

    /**
     * Nested controller map: [version][resource] => AbstractController.
     *
     * @var array
     */
    protected $controllers;

    /**
     * @param \Module $module      The owning module instance.
     * @param array   $controllers Nested [version][resource] controller map.
     */
    public function __construct(
        \Module $module,
        array $controllers
    ) {
        $this->module = $module;

        $this->controllers = $controllers;
    }

    /**
     * Resolve the request to a controller and run it.
     *
     * @throws \Exception If the version/resource combination is unknown.
     *
     * @return void
     */
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

        if (!isset($this->controllers[$version][$resource])) {
            throw new \Exception(
                'Resource not found'
            );
        }

        $controller = $this->controllers[$version][$resource];

        $controller->setContext($context);

        $controller->handle();
    }
}
