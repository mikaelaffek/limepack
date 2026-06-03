<?php

namespace LimepackApi\Classes\Middleware;

use LimepackApi\Classes\Auth\ApiClientProvider;

class AuthMiddleware
{
    protected $provider;

    public function __construct(
        ApiClientProvider $provider
    ) {
        $this->provider = $provider;
    }

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
