# Examples

## Paypal Express checkout

Described in [Get it started](get-it-started.md)

## Stripe.Js

* Configuration

```bash
$ php composer.phar require payum/stripe:@stable payum/payum-laravel-package:@stable
$ php artisan config:publish payum/payum-laravel-package
```

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\LaravelPackage\Action\GetHttpRequestAction;
use Payum\Stripe\Keys;
use Payum\Stripe\PaymentFactory as StripePaymentFactory;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

$getHttpRequestAction = new GetHttpRequestAction();

$stripeJsPayment = StripePaymentFactory::createJs(new Keys(
    'payum.stripe.publishable_key',
    'payum.stripe.secret_key'
));
$stripeJsPayment->addAction($getHttpRequestAction);

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'payments' => array(
        'stripe_js' => $stripeJsPayment,
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
 
         $details = $storage->createModel();
         $details['amount'] = '100';
         $details['currency'] = 'USD';
         $details['description'] = 'a desc';
         $storage->updateModel($details);
 
         $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('stripe_js', $details, 'payment_done');
 
         return \Redirect::to($captureToken->getTargetUrl());
 	}
}
```

## Stripe Checkout

* Configuration

```bash
$ php composer.phar require payum/stripe:@stable payum/payum-laravel-package:@stable
$ php artisan config:publish payum/payum-laravel-package
```

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\LaravelPackage\Action\GetHttpRequestAction;
use Payum\Stripe\Keys;
use Payum\Stripe\PaymentFactory as StripePaymentFactory;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

$getHttpRequestAction = new GetHttpRequestAction();

$stripeJsPayment = StripePaymentFactory::createCheckout(new Keys(
    'payum.stripe.publishable_key',
    'payum.stripe.secret_key'
));
$stripeJsPayment->addAction($getHttpRequestAction);

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'payments' => array(
        'stripe_checkout' => $stripeJsPayment,
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
 
         $details = $storage->createModel();
         $details['amount'] = '100';
         $details['currency'] = 'USD';
         $details['description'] = 'a desc';
         $storage->updateModel($details);
 
         $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('stripe_checkout', $details, 'payment_done');
 
         return \Redirect::to($captureToken->getTargetUrl());
 	}
}
```

## Stripe Direct (via Omnipay)

* Configuration

```bash
$ php composer.phar require payum/omnipay-bridge:@stable payum/payum-laravel-package:@stable
$ php artisan config:publish payum/payum-laravel-package
```

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Omnipay\Common\GatewayFactory;
use Payum\Core\Storage\FilesystemStorage;
use Payum\LaravelPackage\Action\ObtainCreditCardAction;
use Payum\OmnipayBridge\PaymentFactory as OmnipayPaymentFactory;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';

// Stripe Payment
$gatewayFactory = new GatewayFactory;
$gatewayFactory->find();

$stripeGateway = $gatewayFactory->create('Stripe');
$stripeGateway->setApiKey($_SERVER['payum.stripe.secret_key']);
$stripeGateway->setTestMode(true);

$stripePayment = OmnipayPaymentFactory::create($stripeGateway);
$stripePayment->addAction(new ObtainCreditCardAction);

return array(
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'payments' => array(
        'stripe_direct' => $stripePayment,
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
 
         $details = $storage->createModel();
         $details['amount'] = '10.00';
         $details['currency'] = 'USD';
         $storage->updateModel($details);
 
         $captureToken = \App::make('payum.security.token_factory')->createCaptureToken('stripe_direct', $details, 'payment_done');
 
         return \Redirect::to($captureToken->getTargetUrl());
 	}
}
```

Back to [index](index.md).