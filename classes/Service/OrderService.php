<?php

/**
 * Limepack API
 *
 * @author    Limepack Developer Team
 * @copyright Limepack
 * @license   commercial
 */

namespace LimepackApi\Classes\Service;

use LimepackApi\Classes\Repository\OrderRepository;
use LimepackApi\Classes\Exception\NotFoundException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderService {

    protected $repository;

    public function __construct(OrderRepository $repository) {
        $this->repository = $repository;
    }

    public function getOrders(array $pagination): array {
        return $this->repository->findAll(
            $pagination['limit'],
            $pagination['offset']
        );
    }

    public function getOrder(int $id): array {
        $order = new \Order($id);

        if (!\Validate::isLoadedObject($order)) {
            throw new NotFoundException('Order not found');
        }

        $customer = new \Customer($order->id_customer);

        $currentState = new \OrderState((int) $order->getCurrentState());

        return [
            'id_order'   => (int) $order->id,
            'reference'  => $order->reference,
            'total_paid' => (float) $order->total_paid,
            'status'     => is_array($currentState->name)
                ? reset($currentState->name)
                : $currentState->name,
            'customer'   => [
                'id_customer'          => (int) $customer->id,
                'firstname'            => $customer->firstname,
                'lastname'             => $customer->lastname,
                'email'                => $customer->email,
                'id_economic'          => (int) $customer->id_economic,
                'id_employee_designer' => (int) $customer->id_employee_designer,
                'id_employee_contact'  => (int) $customer->id_employee_contact,
                'id_business_type'     => (int) $customer->id_business_type,
                'locations'            => (int) $customer->locations,
                'payment_terms_number' => (int) $customer->payment_terms_number,
                'ean'                  => $customer->ean,
                'invoice_email'        => $customer->invoice_email,
            ],
            'products'   => $order->getProducts(),
        ];
    }
}