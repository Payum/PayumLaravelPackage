<?php

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

return array(
    // You can pass on object or a service id from container.
    'token_storage' => new \Payum\Core\Storage\FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'payments' => array(
        // Put here any payment you want too, omnipay, payex, paypa, be2bill or any other. Here's example of paypal:
        //'paypal_es' => \Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory::create(
        //    new \Payum\Paypal\ExpressCheckout\Nvp\Api(new \Buzz\Client\Curl, array(
        //        'username' => 'REPLACE WITH YOURS',
        //        'password' => 'REPLACE WITH YOURS',
        //        'signature' => 'REPLACE WITH YOURS',
        //        'sandbox' => true
        //    ))
        //),
    ),
    'storages' => array(
        $detailsClass => new \Payum\Core\Storage\FilesystemStorage(__DIR__.'/../../../../storage/payments', $detailsClass),
    )
);