<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Request\NotifyRequest;
use Payum\Core\Request\SecuredNotifyRequest;
use Symfony\Component\HttpFoundation\Request;

class NotifyController extends PayumController
{
    public function doUnsafeAction(Request $request)
    {
        $payment = $this->getPayum()->getPayment($request->get('payment_name'));

        $payment->execute(new NotifyRequest(array_replace(
            $request->query->all(),
            $request->request->all()
        )));

        return \Response::make(null, 204);
    }

    public function doAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $payment->execute(new SecuredNotifyRequest(
            array_replace($request->query->all(), $request->request->all()),
            $token
        ));

        return \Response::make(null, 204);
    }

}