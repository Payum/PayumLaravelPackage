# Get it started.

In this chapter we are going to setup payum package and do simple purchase using paypal express checkout. 
Look at sandbox to find more examples.

## Installation

```bash
php composer.phar require payum/payum-laravel-package payum/xxx
```

_**Note**: Where payum/xxx is a payum package, for example it could be payum/paypal-express-checkout-nvp. Look at [supported gateways](https://github.com/Payum/Core/blob/master/Resources/docs/supported-gateways.md) to find out what you can use._

_**Note**: Use payum/payum if you want to install all gateways at once._

Now you have all codes prepared and ready to be used.

## Configuration

First publish the package configuration files:

```bash
$ php artisan config:publish payum/payum-laravel-package
```

If everything went well you have to have `config.php` in `app/config/packages/payum/payum-larvel-package` directory. 
Let's put some paypal config there:

```php
<?php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\Core\Storage\FilesystemStorage;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

$paypalExpressCheckoutGatewayFactory = new \Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutGatewayFactory();

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'gateways' => array(
        'paypal_ec' => 'acme_payment.paypal_ec',
    ),
    'storages' => array(
        $detailsClass => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $detailsClass),
    )
);
```

## Gateway service

Now we have to add a service definition for `acme_payment.paypal_ec`:

```php
<?php
\App::bind('acme_payment.paypal_ec', function($app) {
    /** @var \Payum\Core\Registry\RegistryInterface $payum */
    $payum = $app['payum'];

    return $payum->getGatewayFactory('paypal_express_checkout')->create([
        'username' => 'EDIT ME',
        'password' => 'EDIT ME',
        'signature' => 'EDIT ME',
        'sandbox' => true
    ]);
});
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

        $details = $storage->create();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
        $details['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->update($details);

        $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('paypal_ec', $details, 'payment_done');

        return \Redirect::to($captureToken->getTargetUrl());
	}
}
```

Here's you may want to modify a `payment_done` route. 
It is a controller where the a payer will be redirected after the payment is done, whenever it is success failed or pending. 
Read a [dedicated chapter](payment_done_controller.md) about how the payment done controller may look like.

Back to [index](index.md).
