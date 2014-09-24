<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Request\Notify;
use Symfony\Component\HttpFoundation\Request;

class NotifyController extends PayumController
{
    public function doUnsafeAction(Request $request)
    {
        $payment = $this->getPayum()->getPayment($request->get('payment_name'));

        $payment->execute(new Notify(null));

        return \Response::make(null, 204);
    }

    public function doAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $payment->execute(new Notify($token));

        return \Response::make(null, 204);
    }
}