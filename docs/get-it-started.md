# Get it started.

In this chapter we are going to setup payum package and do simple purchase using paypal express checkout. 
Look at sandbox to find more examples.

## Installation

```bash
php composer.phar require "payum/payum-laravel-package:*@stable" "payum/paypal-express-checkout-nvp:*@stable"
```

Now you have all codes prepared and ready to be used.

## Configuration

First publish the package configuration files:

```bash
$ php artisan config:publish payum/payum-laravel-package
```

If everything went well you have to have `config.php` in `app/config/packages/payum/payum-larvel-package` directory. 
Let's put some paypal config there:

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\Core\Storage\FilesystemStorage;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory as PaypalPaymentFactory;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

$paypalPayment = PaypalPaymentFactory::create(new Api(array(
    'username' => $_SERVER['payum.paypal_express_checkout.username'],
    'password' => $_SERVER['payum.paypal_express_checkout.password'],
    'signature' => $_SERVER['payum.paypal_express_checkout.signature'],
    'sandbox' => true
)));

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'payments' => array(
        'paypal_es' => $paypalPayment,
    ),
    'storages' => array(
        $detailsClass => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $detailsClass),
    )
);
```

## Prepare payment

Lets create a controller where we prepare the payment details.

```php
<?php
// app/controllers/PaypalController.php

class PaypalController extends BaseController
{
	public function prepareExpressCheckout()
	{
        $storage = \App::make('payum')->getStorage('Payum\Core\Model\ArrayObject');

        $details = $storage->createModel();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
        $details['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->updateModel($details);

        $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('paypal_es', $details, 'payment_done');

        return \Redirect::to($captureToken->getTargetUrl());
	}
}
```

Here's you may want to modify a `payment_done` route. 
It is a controller where the a payer will be redirected after the payment is done, whenever it is success failed or pending. 
Read a [dedicated chapter](payment_done_controller.md) about how the payment done controller may look like.

Back to [index](index.md).