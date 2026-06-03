<?php

/**
 * Limepack API
 *
 * @author    Limepack Developer Team
 * @copyright Limepack
 * @license   commercial
 */

namespace LimepackApi\Classes\Router;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModuleRoutes
{
    /**
     * Add a new resource here — one line.
     * Routes (list / show / action) are generated automatically.
     *
     * NOTE: no typed property here on purpose (PHP 7.2 compatible).
     */
    private $resources = array(
        'orders',
        'tracking',
    );

    public function getRoutes()
    {
        $routes = array();

        $versions = array('v1', 'v2');

        foreach ($versions as $version) {

            foreach ($this->resources as $resource) {

                $routes['limepackapi_' . $resource . '_list'] = array(
                    'rule' => 'limepackapi/{version}/' . $resource,
                    'keywords' => array(
                        'version' => array('regexp' => 'v[0-9]+', 'param' => 'version'),
                    ),
                    'controller' => 'gateway',
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'limepackapi',
                        'resource' => $resource,
                    ),
                );

                $routes['limepackapi_' . $resource . '_show'] = array(
                    'rule' => 'limepackapi/{version}/' . $resource . '/{id}',
                    'keywords' => array(
                        'version' => array('regexp' => 'v[0-9]+', 'param' => 'version'),
                        'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                    ),
                    'controller' => 'gateway',
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'limepackapi',
                        'resource' => $resource,
                    ),
                );

                $routes['limepackapi_' . $resource . '_action'] = array(
                    'rule' => 'limepackapi/{version}/' . $resource . '/{id}/{action}',
                    'keywords' => array(
                        'version' => array('regexp' => 'v[0-9]+', 'param' => 'version'),
                        'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                        'action' => array('regexp' => '[a-z]+', 'param' => 'action'),
                    ),
                    'controller' => 'gateway',
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'limepackapi',
                        'resource' => $resource,
                    ),
                );
            }
        }

        return $routes;
    }
}
