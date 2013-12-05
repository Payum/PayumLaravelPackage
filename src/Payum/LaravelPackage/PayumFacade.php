<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\Facades\Facade;

class PayumFacade extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'payum';
    }

}