<?php
namespace Payum\LaravelPackage\Controller;

use Illuminate\Routing\Controllers\Controller;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;

abstract class PayumController extends \Controller
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
}