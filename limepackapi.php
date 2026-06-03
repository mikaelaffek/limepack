<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class LimepackApi extends Module
{
    public function __construct()
    {
        $this->name = 'limepackapi';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Limepack';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = array(
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_
        );

        parent::__construct();

        $this->displayName = 'Limepack API';
        $this->description = 'Versioned API MVP';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('moduleRoutes');
    }

    public function hookModuleRoutes()
    {
        return array(

            'limepackapi_orders_list' => array(

                'rule' => 'limepackapi/{version}/orders',

                'keywords' => array(

                    'version' => array(
                        'regexp' => 'v[0-9]+',
                        'param' => 'version',
                    ),
                ),

                'controller' => 'gateway',

                'params' => array(
                    'fc' => 'module',
                    'module' => 'limepackapi',
                    'resource' => 'orders',
                ),
            ),

            'limepackapi_orders_show' => array(

                'rule' => 'limepackapi/{version}/orders/{id}',

                'keywords' => array(

                    'version' => array(
                        'regexp' => 'v[0-9]+',
                        'param' => 'version',
                    ),

                    'id' => array(
                        'regexp' => '[0-9]+',
                        'param' => 'id',
                    ),
                ),

                'controller' => 'gateway',

                'params' => array(
                    'fc' => 'module',
                    'module' => 'limepackapi',
                    'resource' => 'orders',
                ),
            ),
        );
    }
}
