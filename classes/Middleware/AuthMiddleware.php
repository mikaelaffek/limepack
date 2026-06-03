<?php

/**
 * Limepack API
 *
 * @author    Limepack Developer Team
 * @copyright Limepack
 * @license   commercial
 */

namespace LimepackApi\Classes\Middleware;

use LimepackApi\Classes\Auth\ApiClientProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Authenticates incoming API requests by validating the caller's API key.
 *
 * This middleware is the single entry-point gatekeeper for the API: every
 * request dispatched through the gateway controller must pass handle()
 * before any resource logic runs. It delegates the actual key lookup to an
 * ApiClientProvider, so the storage mechanism (currently hardcoded, later
 * DB/Configuration) can change without touching this class.
 */
class AuthMiddleware
{
    /**
     * Resolves and validates API keys against the list of known clients.
     *
     * @var ApiClientProvider
     */
    protected $provider;

    /**
     * @param ApiClientProvider $provider Client/key resolver used to validate
     *                                    the incoming key.
     */
    public function __construct(
        ApiClientProvider $provider
    ) {
        $this->provider = $provider;
    }

    /**
     * Authenticate the current request.
     *
     * Reads the "key" parameter from the request (query string or body via
     * PrestaShop's Tools::getValue) and verifies it against the known clients.
     * On success the method returns silently and the request is allowed to
     * proceed; on failure it throws so the caller (AbstractController) can
     * convert the exception into an HTTP error response.
     *
     * @throws \Exception If no key is supplied or the key is not recognized.
     *
     * @return void
     */
    public function handle()
    {
        $key = \Tools::getValue('key');

        if (!$key) {
            throw new \Exception(
                'No API key provided'
            );
        }

        if (!$this->provider->validate($key)) {
            throw new \Exception(
                'Invalid API key'
            );
        }
    }
}
