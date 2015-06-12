<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Request\Notify;
use Symfony\Component\HttpFoundation\Request;

class NotifyController extends PayumController
{
    public function doUnsafeAction(Request $request)
    {
        $gateway = $this->getPayum()->getGateway($request->get('gateway_name'));

        $response = $this->convertReply($gateway->execute(new Notify(null), true));

        if($response)
            return $response;

        return \Response::make(null, 204);
    }

    public function doAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $response = $this->convertReply($gateway->execute(new Notify($token), true));

        if($response)
            return $response;

        return \Response::make(null, 204);
    }
}