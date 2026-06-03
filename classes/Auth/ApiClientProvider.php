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

        'manus' => array(
            'key' => 'lp_manus_9f3a1c72d4',
        ),

        'loveable' => array(
            'key' => 'lp_loveable_7b28de91fa',
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
