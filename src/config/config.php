<?php
/**
 * This is an example config.
 * It shows how to configure paypal express checkout.
 * Publish it to app folder and modify for your needs.
 */

use Buzz\Client\Curl;
use Payum\Extension\StorageExtension;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory;
use Payum\Storage\FilesystemStorage;

$detailsClass = 'Application\Model\PaymentDetails';

return array(
    'payum' => array(
        'token_storage' => new FilesystemStorage(
            __DIR__.'/../../data',
            'Application\Model\PaymentSecurityToken',
        ),
        'payments' => array(
            'paypal' => PaymentFactory::create(new Api(new Curl(), array(
                'username' => 'REPLACE WITH YOURS',
                'password' => 'REPLACE WITH YOURS',
                'signature' => 'REPLACE WITH YOURS',
                'sandbox' => true
            )))
        ),
        'storages' => array(
            'paypal' => array(
                $detailsClass => new FilesystemStorage(__DIR__.'/../../data', $detailsClass),
            )
        )
    ),
);