<?php
namespace Payum\LaravelPackage;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\CoreGatewayFactory as BaseCoreGatewayFactory;
use Payum\Core\Gateway;

class CoreGatewayFactory extends BaseCoreGatewayFactory
{
    /**
     * @var Application
     */
    private $container;

    /**
     * @param Application $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Gateway     $gateway
     * @param ArrayObject $config
     */
    protected function buildActions(Gateway $gateway, ArrayObject $config)
    {
        foreach ($config as $name => $value) {
            if (0 === strpos($name, 'payum.action') && false == is_object($config[$name])) {
                $config[$name] = $this->container[$config[$name]];
            }
        }

        parent::buildActions($gateway, $config);
    }

    /**
     * @param Gateway     $gateway
     * @param ArrayObject $config
     */
    protected function buildApis(Gateway $gateway, ArrayObject $config)
    {
        foreach ($config as $name => $value) {
            if (0 === strpos($name, 'payum.api') && false == is_object($config[$name])) {
                $config[$name] = $this->container[$config[$name]];
            }
        }

        parent::buildApis($gateway, $config);
    }

    /**
     * @param Gateway     $gateway
     * @param ArrayObject $config
     */
    protected function buildExtensions(Gateway $gateway, ArrayObject $config)
    {
        foreach ($config as $name => $value) {
            if (0 === strpos($name, 'payum.extension') && false == is_object($config[$name])) {
                $config[$name] = $this->container[$config[$name]];
            }
        }

        parent::buildExtensions($gateway, $config);
    }
}