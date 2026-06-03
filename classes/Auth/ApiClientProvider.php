<?php

namespace LimepackApi\Classes\Auth;

class ApiClientProvider
{
    /**
     * TODO:
     * Replace hardcoded keys
     * with DB/config storage.
     */

    protected const CLIENTS = array(

        'supplier_app' => array(
            'key' => 'lp_supplier_9f3a1c72d4',
        ),

        'mobile_app' => array(
            'key' => 'lp_mobile_7b28de91fa',
        ),
    );

    public function validate($key)
    {
        foreach (self::CLIENTS as $client) {

            if ($client['key'] === $key) {
                return true;
            }
        }

        return false;
    }
}
