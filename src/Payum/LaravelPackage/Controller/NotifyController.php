<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Request\Notify;
use Symfony\Component\HttpFoundation\Request;

class NotifyController extends PayumController
{
    public function doUnsafeAction(Request $request)
    {
        $gateway = $this->getPayum()->getGateway($request->get('gateway_name'));

        $gateway->execute(new Notify(null));

        return \Response::make(null, 204);
    }

    public function doAction(Request $request)
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);

        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        try {
            $gateway->execute(new Notify($token));
        } catch (ReplyInterface $reply) {
           return $this->convertReply($reply);
        }

        return \Response::make(null, 204);
    }
}