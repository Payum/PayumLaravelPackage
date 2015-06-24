<?php
namespace Payum\LaravelPackage\Controller;

use Illuminate\Routing\Controller;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;

use Payum\Core\Exception\LogicException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Reply\ReplyInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class PayumController extends Controller
{
    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return \App::make('payum');
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    protected function getHttpRequestVerifier()
    {
        return \App::make('payum.security.http_request_verifier');
    }

    protected function convertReply($reply)
    {
        if(!$reply instanceof ReplyInterface) {
            return;
        }

        if($this->shouldThrowExceptions()) {
            throw $reply;
        }

        $response = null;

        if ($reply instanceof SymfonyHttpResponse) {
            $response = $reply->getResponse();
        } elseif ($reply instanceof HttpRedirect) {
            $response = new RedirectResponse($reply->getUrl());
        } elseif ($reply instanceof HttpResponse) {
            $response = new Response($reply->getContent());
        } 
        if ($response) {
            return $response;
        }

        $ro = new \ReflectionObject($reply);
        throw new LogicException(
            sprintf('Cannot convert reply %s to Laravel response.', $ro->getShortName()),
            null,
            $reply
        );
    }

    protected function shouldThrowExceptions()
    {
        $l4Value = \Config::get('payum-laravel-package::settings.throwReplyExceptions');
        $l5Value = \Config::get('payum-laravel-package.settings.throwReplyExceptions');
        return $l4Value || $l5Value;
    }
}