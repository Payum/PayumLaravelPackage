<?php
namespace Payum\LaravelPackage\Security;

use Payum\Core\Security\AbstractGenericTokenFactory;

class TokenFactory extends AbstractGenericTokenFactory
{
    /**
     * {@inheritDoc}
     */
    protected function generateUrl($path, array $parameters = array())
    {
        return \URL::route($path, $parameters);
    }
}