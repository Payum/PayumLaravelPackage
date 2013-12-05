<?php
namespace Payum\LaravelPackage\Controller;

use Symfony\Component\HttpKernel\Controller;

class CaptureController extends Controller
{
    public function doAction()
    {
        $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $status = new BinaryMaskStatusRequest($token);
        $payment->execute($status);
        if (false == $status->isNew()) {
            throw new HttpException(400, 'The model status must be new.');
        }

        $payment->execute(new SecuredCaptureRequest($token));

        $this->getHttpRequestVerifier()->invalidate($token);

        return $this->redirect($token->getAfterUrl());
    }
} 