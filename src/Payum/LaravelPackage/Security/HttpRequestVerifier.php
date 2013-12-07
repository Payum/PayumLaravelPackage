<?php
namespace Payum\LaravelPackage\Security;

use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\StorageInterface;

class HttpRequestVerifier implements HttpRequestVerifierInterface
{
    /**
     * @var \Payum\Core\Storage\StorageInterface
     */
    protected $tokenStorage;

    /**
     * @param StorageInterface $tokenStorage
     */
    public function __construct(StorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public function verify($httpRequest)
    {
        if (false === $hash = \Input::get('payum_token')) {
            \App::abort(404, 'Token parameter not set in request');
        }

        if ($hash instanceof TokenInterface) {
            $token = $hash;
        } else {
            if (false == $token = $this->tokenStorage->findModelById($hash)) {
                \App::abort(404, sprintf('A token with hash `%s` could not be found.', $hash));
            }

            if (\Request::path() != parse_url($token->getTargetUrl(), PHP_URL_PATH)) {
                \App::abort(404, sprintf('The current url %s not match target url %s set in the token.', \Request::path(), $token->getTargetUrl()));
            }
        }

        return $token;
    }

    /**
     * {@inheritDoc}
     */
    public function invalidate(TokenInterface $token)
    {
        $this->tokenStorage->deleteModel($token);
    }
}