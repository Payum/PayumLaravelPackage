<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Request\Refund;
use Symfony\Component\HttpFoundation\Request;

class RefundController extends PayumController
{
    public function doAction($payum_token)
    {
        /** @var Request $request */
        $request = \App::make('request');
        $request->attributes->set('payum_token', $payum_token);

        $token = $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $payment->execute(new Refund($token));

        $this->getHttpRequestVerifier()->invalidate($token);

        if($token->getAfterUrl()){
            return \Redirect::to($token->getAfterUrl());
        }

        return \Response::make(null, 204);

    }
}