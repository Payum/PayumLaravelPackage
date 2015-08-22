# Examples

## Paypal Express checkout

Described in [Get it started](get-it-started.md)

## Stripe.Js

* Configuration

```bash
$ php composer.phar require payum/payum-laravel-package stripe/stripe-php payum/stripe
$ php artisan config:publish payum/payum-laravel-package
```

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\LaravelPackage\Action\GetHttpRequestAction;
use Payum\Core\Storage\FilesystemStorage;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

$stripeJsGatewayFactory = new \Payum\Stripe\StripeJsGatewayFactory();

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'gateways' => array(
        'stripe_js' => $stripeJsGatewayFactory->create(array(
            'publishable_key' => $_SERVER['payum.stripe.publishable_key'],
            'secret_key' => $_SERVER['payum.stripe.secret_key'],
            'payum.action.get_http_request' => new GetHttpRequestAction(),
        )),
    ),
    'storages' => array(
        $detailsClass => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $detailsClass),
    )
);
```

* Prepare payment

```php
<?php
// app/controllers/PaypalController.php

cclass StripeController extends BaseController
{
 	public function prepareJs()
 	{
         $storage = \App::make('payum')->getStorage('Payum\Core\Model\ArrayObject');
 
         $details = $storage->create();
         $details['amount'] = '100';
         $details['currency'] = 'USD';
         $details['description'] = 'a desc';
         $storage->update($details);
 
         $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('stripe_js', $details, 'payment_done');
 
         return \Redirect::to($captureToken->getTargetUrl());
 	}
}
```

## Stripe Checkout

* Configuration

```bash
$ php composer.phar require payum/stripe payum/payum-laravel-package stripe/stripe-php
$ php artisan config:publish payum/payum-laravel-package
```

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\LaravelPackage\Action\GetHttpRequestAction;
use Payum\Core\Storage\FilesystemStorage;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

$stripeCheckoutGatewayFactory = new \Payum\Stripe\StripeCheckoutGatewayFactory();

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'gateways' => array(
        'stripe_checkout' => $stripeCheckoutGatewayFactory->create(array(
            'publishable_key' => 'EDIT ME',
            'secret_key' => 'EDIT ME',
            'payum.action.get_http_request' => new GetHttpRequestAction(),
        )),
    ),
    'storages' => array(
        $detailsClass => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $detailsClass),
    )
);
```

* Prepare payment

```php
<?php
// app/controllers/PaypalController.php

cclass StripeController extends BaseController
{
 	public function prepareCheckout()
 	{
         $storage = \App::make('payum')->getStorage('Payum\Core\Model\ArrayObject');
 
         $details = $storage->create();
         $details['amount'] = '100';
         $details['currency'] = 'USD';
         $details['description'] = 'a desc';
         $storage->update($details);
 
         $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('stripe_checkout', $details, 'payment_done');
 
         return \Redirect::to($captureToken->getTargetUrl());
 	}
}
```

## Stripe Direct (via Omnipay)

* Configuration

```bash
$ php composer.phar require payum/omnipay-bridge payum/payum-laravel-package omnipay/stripe
$ php artisan config:publish payum/payum-laravel-package
```

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\LaravelPackage\Action\GetHttpRequestAction;
use Payum\Core\Storage\FilesystemStorage;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

$omnipayDirectGatewayFactory = new \Payum\OmnipayBridge\OmnipayDirectGatewayFactory();

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'gateways' => array(
        'stripe_direct' => $omnipayDirectGatewayFactory->create(array(
            'type' => 'Stripe',
            'options' => array(
                'apiKey' => 'EDIT ME',
                'testMode' => true,
            ),
            'payum.action.obtain_credit_card' => new ObtainCreditCardAction,
        )),
    ),
    'storages' => array(
        $detailsClass => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $detailsClass),
    )
);
```

* Prepare payment

```php
<?php
// app/controllers/PaypalController.php

cclass StripeController extends BaseController
{
 	public function prepareDirect()
 	{
         $storage = \App::make('payum')->getStorage('Payum\Core\Model\ArrayObject');
 
         $details = $storage->create();
         $details['amount'] = '10.00';
         $details['currency'] = 'USD';
         $storage->update($details);
 
         $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('stripe_direct', $details, 'payment_done');
 
         return \Redirect::to($captureToken->getTargetUrl());
 	}
}
```

Back to [index](index.md).