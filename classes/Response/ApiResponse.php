<?php

namespace LimepackApi\Classes\Response;

class ApiResponse
{
    public static function success(
        array $data = array()
    ) {
        return array(
            'success' => true,
            'data' => $data,
        );
    }

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
