<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Payum\LaravelPackage\Security\HttpRequestVerifier;

class PayumServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->package('payum/payum-laravel-package');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app['payum'] = $this->app->share(function($app) {
            $payum = new Payum(
                $app['config']['payum/payum-laravel-package::payments'],
                $app['config']['payum/payum-laravel-package::storages']
            );

            $payum->setContainer($app);
            $payum->registerStorageExtensions();

            return $payum;
        });

        $this->app['payum.security.token_storage'] = $this->app->share(function($app) {
            $tokenStorage = $app['config']['payum/payum-laravel-package::token_storage'];

            return is_object($tokenStorage) ? $tokenStorage : $app[$tokenStorage];
        });

        $this->app['payum.security.http_request_verifier'] = $this->app->share(function($app) {
            return new HttpRequestVerifier($app['payum.security.token_storage']);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return array(
            'payum',
            'payum.security.token_storage',
            'payum.security.http_request_verifier',
        );
    }
}