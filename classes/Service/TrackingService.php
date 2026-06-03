<?php

/**
 * Limepack API
 *
 * @author    Limepack Developer Team
 * @copyright Limepack
 * @license   commercial
 */

namespace LimepackApi\Classes\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use LimepackApi\Classes\Exception\NotFoundException;

class TrackingService {

    public function update(int $orderId, array $body, $client): array {
        $order = new \Order($orderId);

        if (!\Validate::isLoadedObject($order)) {
            throw new NotFoundException('Order not found');
        }

        // Update tracking number
        $order->shipping_number = $body['tracking_number'];
        $order->carrier_tracking_url = $body['carrier'] ?? '';
        $order->update();

        // Update order status to shipped — fires PS hook naturally
        // PS handles emails, logs, everything
        $history = new \OrderHistory();
        $history->id_order = $order->id;
        $history->changeIdOrderState(
            \Configuration::get('PS_OS_SHIPPING'),
            $order,
            true
        );
        $history->addWithemail(true);

        // Log who made the change
        \PrestaShopLogger::addLog(
            sprintf(
                '[API] Tracking %s updated on order %s by [%s]',
                $body['tracking_number'],
                $order->reference,
                $client->name ?? 'unknown'
            ),
            1,
            null,
            'Order',
            $order->id,
            true
        );

        return [
            'id_order'        => (int) $order->id,
            'reference'       => $order->reference,
            'tracking_number' => $order->shipping_number,
            'carrier'         => $body['carrier'] ?? '',
        ];
    }
}