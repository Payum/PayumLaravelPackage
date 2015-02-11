<?php
namespace Payum\LaravelPackage\Security;

use Payum\Core\Security\AbstractTokenFactory;

class TokenFactory extends AbstractTokenFactory
{
    /**
     * {@inheritDoc}
     */
    protected function generateUrl($path, array $parameters = array())
    {
        return \URL::route($path, $parameters);
    }
}