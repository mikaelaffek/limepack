<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Limepack Developer Team
 *  @copyright Limepack
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Limepack API module.
 *
 * Entry point for the versioned Limepack REST API. The module itself stays
 * intentionally thin: it only declares the module metadata, installs the
 * required hook, and exposes the API routes. All request handling lives in
 * the gateway front controller and the classes/ namespace (auth, routing,
 * services, repositories, responses).
 */
class LimepackApi extends Module
{
    /**
     * Sets the module metadata and PrestaShop version compatibility.
     */
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

    /**
     * Installs the module and registers the moduleRoutes hook used to
     * expose the API endpoints.
     *
     * @return bool True on successful installation, false otherwise.
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('moduleRoutes');
    }

    /**
     * Registers the API URL routes with PrestaShop's dispatcher.
     *
     * Route generation is delegated to ModuleRoutes so that adding a new
     * API resource only requires a one-line change in that class.
     *
     * @return array The route definitions consumed by the moduleRoutes hook.
     */
    public function hookModuleRoutes()
    {
        require_once dirname(__FILE__) . '/classes/Router/ModuleRoutes.php';

        $moduleRoutes = new \LimepackApi\Classes\Router\ModuleRoutes();

        return $moduleRoutes->getRoutes();
    }
}
