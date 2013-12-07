<?php
namespace Payum\LaravelPackage;

use Illuminate\Container;

class Payum extends SimpleRegistry
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Payum\Core\Storage\StorageInterface
     */
    public function getTokenStorage()
    {
        return $this->container['payum.security.token_storage'];
    }

    /**
     * @return \Payum\Core\Security\HttpRequestVerifierInterface
     */
    public function getHttpRequestVerifier()
    {
        return $this->container['payum.security.http_request_verifier'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getService($id)
    {
        return is_object($id) ? $id : $this->container[$id];
    }
} 