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
It contains an example of how the config look like. You can change or add anything you need in that file. 
Lets modify it a bit. You may remove Stripe section as it is not needed right now. 
Change filesystem storage to Doctrine. Register other payments. Now we modify these lines, and set correct Paypal credentials:

```php
// app/config/packages/payum/payum-laravel-package/config.php

$paypalPayment = PaypalPaymentFactory::create(new Api(new Curl, array(
    'username' => 'aUsername',
    'password' => 'aPassword',
    'signature' => 'aSignature',
    'sandbox' => true
)));
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
        $storage = $this->getPayum()->getStorage('Payum\Core\Model\ArrayObject');

        $details = $storage->createModel();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
        $details['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->updateModel($details);

        $captureToken = $this->getTokenFactory()->createCaptureToken('paypal_es', $details, 'payment_done');
        $details['RETURNURL'] = $captureToken->getTargetUrl();
        $details['CANCELURL'] = $captureToken->getTargetUrl();
        $storage->updateModel($details);

        return \Redirect::to($captureToken->getTargetUrl());
	}

    /**
     * @return \Payum\Core\Registry\RegistryInterface
     */
    protected function getPayum()
    {
        return \App::make('payum');
    }

    /**
     * @return \Payum\Core\Security\GenericTokenFactoryInterface
     */
    protected function getTokenFactory()
    {
        return \App::make('payum.security.token_factory');
    }
}
```

Here's you may want to modify a `payment_done` route. 
It is a controller where the a payer will be redirected after the payment is done, whenever it is success failed or pending. 
Read a [dedicated chapter](payment_done_controller.md) about how the payment done controller may look like.

Back to [index](index.md).