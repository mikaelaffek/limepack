<?php

namespace LimepackApi\Classes\Api;

use LimepackApi\Classes\Response\ApiResponse;

abstract class AbstractController
{
    protected $module;

    protected $service;

    protected $authMiddleware;

    protected $context = array();

    public function __construct(
        \Module $module,
        $service,
        $authMiddleware
    ) {
        $this->module = $module;

        $this->service = $service;

        $this->authMiddleware = $authMiddleware;
    }

    public function setContext(array $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        try {

            $this->authMiddleware->handle();

            $method = strtoupper(
                $_SERVER['REQUEST_METHOD']
            );

            switch ($method) {

                case 'GET':

                    $data = $this->get();

                    break;

                default:

                    $this->respondError(
                        405,
                        'Method not allowed'
                    );
            }

            $this->respond(
                200,
                $data
            );

        } catch (\Exception $e) {

            $this->respondError(
                500,
                $e->getMessage()
            );
        }
    }

    protected function respond(
        $code,
        array $data
    ) {
        http_response_code($code);

        header('Content-Type: application/json');

        die(json_encode(
            ApiResponse::success($data)
        ));
    }

    protected function respondError(
        $code,
        $message
    ) {
        http_response_code($code);

        header('Content-Type: application/json');

        die(json_encode(
            ApiResponse::error(
                $message,
                $code
            )
        ));
    }

    abstract protected function get();
}
