<?php

/**
 * Limepack API
 *
 * @author    Limepack Developer Team
 * @copyright Limepack
 * @license   commercial
 */

namespace LimepackApi\Classes\Exception;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Thrown when a requested resource (e.g. an order) does not exist.
 *
 * Carries a 404 status so the controller layer can translate it into the
 * correct HTTP response instead of a generic 500.
 */
class NotFoundException extends \Exception
{
    /**
     * @var int
     */
    protected $statusCode = 404;

    /**
     * @return int The HTTP status code associated with this exception.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
