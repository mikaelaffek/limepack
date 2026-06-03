<?php

namespace LimepackApi\Classes\Response;

/**
 * Builds the consistent JSON envelope used by every API response.
 *
 * All endpoints return a predictable shape so consumers (internal AI/no-code
 * tools) can reliably branch on the "success" flag:
 *   success: { "success": true,  "data": {...} }
 *   error:   { "success": false, "error": { "code": ..., "message": ... } }
 */
class ApiResponse
{
    /**
     * Build a success envelope.
     *
     * @param array $data The payload to return under the "data" key.
     *
     * @return array The success-shaped response array, ready for json_encode().
     */
    public static function success(
        array $data = array()
    ) {
        return array(
            'success' => true,
            'data' => $data,
        );
    }

    /**
     * Build an error envelope.
     *
     * @param string $message Human-readable error message.
     * @param int    $code    Error/HTTP status code describing the failure.
     *
     * @return array The error-shaped response array, ready for json_encode().
     */
    public static function error(
        $message,
        $code
    ) {
        return array(
            'success' => false,

            'error' => array(
                'code' => $code,
                'message' => $message,
            ),
        );
    }
}
