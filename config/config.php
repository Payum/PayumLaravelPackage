<?php

use Payum\Core\Storage\FilesystemStorage;

return array(
    'payum' => array(
        'token_storage' => new FilesystemStorage(__DIR__.'/../../data', 'Payum\Core\Model\Token', 'hash'),
        'payments' => array(
//            'paypal' => \Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory::create(
//                new \Payum\Paypal\ExpressCheckout\Nvp\Api(new \Buzz\Client\Curl, array(
//                    'username' => 'REPLACE WITH YOURS',
//                    'password' => 'REPLACE WITH YOURS',
//                    'signature' => 'REPLACE WITH YOURS',
//                    'sandbox' => true
//            )))
        ),
        'storages' => array(
            'Payum\Core\Model\ArrayObject' => new FilesystemStorage(__DIR__.'/../../data', $detailsClass),
        )
    ),
);