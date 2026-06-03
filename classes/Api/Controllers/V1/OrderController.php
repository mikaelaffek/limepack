<?php

namespace LimepackApi\Classes\Api\Controllers\V1;

use LimepackApi\Classes\Api\AbstractController;

class OrderController extends AbstractController
{
    protected function get()
    {
        $id = $this->context['id'];

        if ($id) {
            return $this->service->getOrder(
                (int)$id
            );
        }

        return array(
            'orders' => $this->service->getOrders(
                $this->context['pagination']
            ),
        );
    }
}
