<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Request\BinaryMaskStatusRequest;
use Payum\Core\Request\SecuredCaptureController;

class CaptureController extends BaseController
{
    public function doAction()
    {
        $token = \Payum::getHttpRequestVerifier()->verify(null);

        $payment = \Payum::getPayment($token->getPaymentName());

        $status = new BinaryMaskStatusRequest($token);
        $payment->execute($status);
        if (false == $status->isNew()) {
            \App::abort(400, 'The model status must be new.');
        }

        $payment->execute(new SecuredCaptureRequest($token));

        \Payum::getHttpRequestVerifier()->invalidate($token);

        return \Redirect::to($token->getAfterUrl());
    }
}