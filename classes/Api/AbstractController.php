<?php

namespace LimepackApi\Classes\Api;

use LimepackApi\Classes\Response\ApiResponse;

/**
 * Base class for all versioned API controllers.
 *
 * Provides the shared request lifecycle: authenticate the caller, route the
 * request by HTTP method, and serialize the result (or any error) into a
 * consistent JSON envelope via ApiResponse. Concrete controllers only need to
 * implement the resource-specific logic (e.g. get()).
 */
abstract class AbstractController
{
    /**
     * The owning module instance.
     *
     * @var \Module
     */
    protected $module;

    /**
     * Domain service used by the concrete controller to fulfil the request
     * (e.g. OrderService). Loosely typed so each controller can inject its own.
     *
     * @var mixed
     */
    protected $service;

    /**
     * Authentication gatekeeper run before any resource logic.
     *
     * @var \LimepackApi\Classes\Middleware\AuthMiddleware
     */
    protected $authMiddleware;

    /**
     * Per-request context (route id, pagination, etc.) set by the router.
     *
     * @var array
     */
    protected $context = array();

    /**
     * @param \Module $module         The owning module instance.
     * @param mixed   $service        Domain service for this controller.
     * @param mixed   $authMiddleware Authentication middleware to run first.
     */
    public function __construct(
        \Module $module,
        $service,
        $authMiddleware
    ) {
        $this->module = $module;

        $this->service = $service;

        $this->authMiddleware = $authMiddleware;
    }

    /**
     * Inject the per-request context resolved by the router.
     *
     * @param array $context Route parameters and pagination data.
     *
     * @return void
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * Run the full request lifecycle.
     *
     * Authenticates the request, dispatches by HTTP method, and emits the
     * response. Any thrown exception is caught and converted into a JSON
     * error response so the API never leaks an uncaught fatal/500 stack trace.
     * Note: responses are terminal (die()), so this method does not return.
     *
     * @return void
     */
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

    /**
     * Emit a successful JSON response and terminate the request.
     *
     * @param int   $code HTTP status code to send (e.g. 200).
     * @param array $data Payload wrapped by ApiResponse::success().
     *
     * @return void
     */
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

    /**
     * Emit an error JSON response and terminate the request.
     *
     * @param int    $code    HTTP status code to send (e.g. 401, 404, 500).
     * @param string $message Human-readable error message.
     *
     * @return void
     */
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

    /**
     * Handle a GET request for the concrete resource.
     *
     * @return array The data payload to be wrapped in a success response.
     */
    abstract protected function get();
}
