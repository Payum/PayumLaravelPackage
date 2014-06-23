<?php

use Buzz\Client\Curl;
use Omnipay\Common\GatewayFactory;
use Payum\Core\Storage\FilesystemStorage;
use Payum\LaravelPackage\Action\ObtainCreditCardAction;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory as PaypalPaymentFactory;
use Payum\OmnipayBridge\PaymentFactory as OmnipayPaymentFactory;

$detailsClass = 'Payum\Core\Model\ArrayObject';
$tokenClass = 'Payum\Core\Model\Token';


// Stripe Payment
$gatewayFactory = new GatewayFactory;
$gatewayFactory->find();

$stripeGateway = $gatewayFactory->create('Stripe');
$stripeGateway->setApiKey($_SERVER['payum.stripe.api_key']);
$stripeGateway->setTestMode(true);

$stripePayment = OmnipayPaymentFactory::create($stripeGateway);
$stripePayment->addAction(new ObtainCreditCardAction);

// Paypal Payment

$paypalPayment = PaypalPaymentFactory::create(new Api(new Curl, array(
    'username' => $_SERVER['payum.paypal_express_checkout.username'],
    'password' => $_SERVER['payum.paypal_express_checkout.password'],
    'signature' => $_SERVER['payum.paypal_express_checkout.signature'],
    'sandbox' => true
)));

return array(
    // You can pass on object or a service id from container.
    'token_storage' => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $tokenClass, 'hash'),
    'payments' => array(
        // Put here any payment you want too, omnipay, payex, paypa, be2bill or any other. Here's example of paypal and stripe:
//        'paypal_es' => $paypalPayment,
//        'omnipay_stripe' => $stripePayment,
    ),
    'storages' => array(
        $detailsClass => new FilesystemStorage(__DIR__.'/../../../../storage/payments', $detailsClass),
    )
);