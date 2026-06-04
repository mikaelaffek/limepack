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

        return [
            'order'            => $this->getOrderBasicInfo($order),
            'status'           => $this->getOrderStatus($order),
            'currency'         => $this->getOrderCurrency($order),
            'carrier'          => $this->getOrderCarrier($order),
            'delivery_address' => $this->getDeliveryAddress($order),
            'invoice_address'  => $this->getInvoiceAddress($order),
            'customer'         => $this->getOrderCustomer($order),
            'products'         => $order->getProducts(),
            'payments'         => $order->getOrderPayments(),
            'shipping'         => $order->getShipping(),
            'discounts'        => $order->getCartRules(),
            'history'          => $order->getHistory($order->id_lang),
        ];
    }

    protected function getOrderBasicInfo(\Order $order): array {
        return [
            'id_order'            => (int) $order->id,
            'reference'           => $order->reference,
            'date_add'            => $order->date_add,
            'total_paid'          => (float) $order->total_paid,
            'total_paid_tax_incl' => (float) $order->total_paid_tax_incl,
            'total_paid_tax_excl' => (float) $order->total_paid_tax_excl,
            'total_shipping'      => (float) $order->total_shipping,
            'total_discounts'     => (float) $order->total_discounts,
            'payment'             => $order->payment,
        ];
    }

    protected function getOrderStatus(\Order $order): string {
        $currentState = new \OrderState((int) $order->getCurrentState());
        return is_array($currentState->name)
            ? reset($currentState->name)
            : $currentState->name;
    }

    protected function getOrderCurrency(\Order $order): array {
        $currency = new \Currency($order->id_currency);
        return [
            'id_currency' => (int) $currency->id,
            'iso_code'    => $currency->iso_code,
            'symbol'      => $currency->sign,
        ];
    }

    protected function getOrderCarrier(\Order $order): array {
        $carrier = new \Carrier($order->id_carrier);
        return [
            'id_carrier' => (int) $carrier->id,
            'name'       => $carrier->name,
        ];
    }

    protected function getDeliveryAddress(\Order $order): array {
        $address = new \Address($order->id_address_delivery);
        return [
            'id_address' => (int) $address->id,
            'country'    => \Country::getIsoById($address->id_country),
            'city'       => $address->city,
            'address1'   => $address->address1,
            'address2'   => $address->address2,
            'postcode'   => $address->postcode,
        ];
    }

    protected function getInvoiceAddress(\Order $order): array {
        $address = new \Address($order->id_address_invoice);
        return [
            'id_address' => (int) $address->id,
            'country'    => \Country::getIsoById($address->id_country),
            'city'       => $address->city,
            'address1'   => $address->address1,
            'address2'   => $address->address2,
            'postcode'   => $address->postcode,
        ];
    }

    protected function getOrderCustomer(\Order $order): array {
        $customer = $order->getCustomer();
        return [
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
        ];
    }
}