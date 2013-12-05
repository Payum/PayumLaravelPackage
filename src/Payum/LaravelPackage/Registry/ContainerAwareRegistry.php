<?php
namespace Payum\LaravelPackage;

use Illuminate\Container;

class ContainerAwareRegistry extends SimpleRegistry
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
     * {@inheritDoc}
     */
    protected function getService($id)
    {
        return is_object($id) ? $id : $this->container[$id];
    }
} 