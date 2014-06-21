<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Request\SecuredCaptureRequest;
use Symfony\Component\HttpFoundation\Request;

class CaptureController extends PayumController
{
    public function doAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $payment->execute(new SecuredCaptureRequest($token));

        $this->getHttpRequestVerifier()->invalidate($token);

        return \Redirect::to($token->getAfterUrl());
    }
}