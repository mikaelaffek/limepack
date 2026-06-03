<?php

/**
 * Limepack API
 *
 * @author    Limepack Developer Team
 * @copyright Limepack
 * @license   commercial
 */

namespace LimepackApi\Classes\Api\Controllers\V1;

use LimepackApi\Classes\Api\AbstractController;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * V1 controller for the "tracking" resource.
 *
 * Exposes a write/action endpoint that sets an order's tracking number and
 * advances it to the shipped state. Delegates all business logic to
 * TrackingService; no repository is involved (it operates directly on
 * PrestaShop's Order/OrderHistory models).
 */
class TrackingController extends AbstractController
{
    /**
     * Update tracking for an order and mark it as shipped.
     *
     * Accepts either a JSON body or standard POST fields:
     *   - tracking_number (required)
     *   - carrier         (optional)
     *
     * @return array The updated tracking payload.
     */
    protected function post()
    {
        $id = (int) $this->context['id'];

        $body = $this->getJsonBody();

        if (empty($body['tracking_number'])) {
            $body['tracking_number'] = \Tools::getValue('tracking_number');
        }

        if (empty($body['carrier'])) {
            $body['carrier'] = \Tools::getValue('carrier');
        }

        if (empty($body['tracking_number'])) {
            $this->respondError(
                422,
                'tracking_number is required'
            );
        }

        return $this->service->update($id, $body, null);
    }
}
