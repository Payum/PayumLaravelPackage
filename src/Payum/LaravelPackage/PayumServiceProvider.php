<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

class PayumServiceProvider extends ServiceProvider
{

    /**
     * Version Specific provider
     */
    protected $provider;

    /**
     * Create a new service provider instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->provider = $this->getProvider();
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->provider->boot();
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->provider->register();
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return $this->provider->provides();
    }

    /**
     *  Return ServiceProvider for current Laravel Version
     * 
     * @return \Illuminate\Support\ServiceProvider
     */
    protected function getProvider()
    {
        $provider = version_compare(Application::VERSION, '5.0', '<')
            ? __NAMESPACE__.'\Providers\Laravel4'
            : __NAMESPACE__.'\Providers\Laravel5';

        return new $provider($this->app);
    }
}