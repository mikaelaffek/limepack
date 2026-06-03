<?php

namespace LimepackApi\Classes\Repository;

class OrderRepository
{
    public function findAll(
        $limit,
        $offset
    ) {
        $query = new \DbQuery();

        $query->select('
            o.id_order,
            o.reference,
            o.total_paid
        ');

        $query->from('orders', 'o');

        $query->orderBy('o.id_order DESC');

        $query->limit(
            (int)$limit,
            (int)$offset
        );

        return \Db::getInstance()->executeS(
            $query
        );
    }
}
