<?php
namespace Payum\LaravelPackage\Facades;

use Illuminate\Support\Facades\Facade;

class Payum extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'payum';
    }

}