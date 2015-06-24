<?php

namespace Payum\LaravelPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier;
use Payum\Core\Security\GenericTokenFactory;
use Payum\LaravelPackage\Registry\ContainerAwareRegistry;
use Payum\LaravelPackage\Security\TokenFactory;

class Laravel5 extends ServiceProvider
{

    /**
     * Version Specific provider
     */
    protected $provider;

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../../config/config.php' => config_path('payum-laravel-package.php'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../../../views', 'payum/payum');

        $this->defineRoutes();
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/config.php', 'payum-laravel-package'
        );

        $this->app['payum'] = $this->app->share(function($app) {
            //TODO add exceptions if invalid gateways and storages options set.

            $payum = new ContainerAwareRegistry(
                \Config::get('payum-laravel-package.gateways'),
                \Config::get('payum-laravel-package.storages')
            );

            $payum->setContainer($app);

            return $payum;
        });

        $this->app['payum.security.token_storage'] = $this->app->share(function($app) {
            //TODO add exceptions if invalid gateways and storages options set.

            $tokenStorage = \Config::get('payum-laravel-package.token_storage');

            return is_object($tokenStorage) ? $tokenStorage : $app[$tokenStorage];
        });

        $this->app['payum.security.token_factory'] = $this->app->share(function($app) {
            return new GenericTokenFactory(
                new TokenFactory($app['payum.security.token_storage'], $app['payum']),
                array(
                    'capture' => 'payum_capture_do',
                    'notify' => 'payum_notify_do',
                    'authorize' => 'payum_authorize_do',
                    'refund' => 'payum_refund_do',
                )
            );
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
            'payum.security.token_factory',
            'payum.security.http_request_verifier',
        );
    }

    /**
     * Define all package routes with Laravel router
     */
    protected function defineRoutes(){
        $route = $this->app->make('router');

        $route->any('/payment/authorize/{payum_token}', array(
            'as' => 'payum_authorize_do',
            'uses' => 'Payum\LaravelPackage\Controller\AuthorizeController@doAction'
        ));

        $route->any('/payment/capture/{payum_token}', array(
            'as' => 'payum_capture_do',
            'uses' => 'Payum\LaravelPackage\Controller\CaptureController@doAction'
        ));

        $route->any('/payment/refund/{payum_token}', array(
            'as' => 'payum_refund_do',
            'uses' => 'Payum\LaravelPackage\Controller\RefundController@doAction'
        ));

        $route->get('/payment/notify/{payum_token}', array(
            'as' => 'payum_notify_do',
            'uses' => 'Payum\LaravelPackage\Controller\NotifyController@doAction'
        ));

        $route->get('/payment/notify/unsafe/{gateway_name}', array(
            'as' => 'payum_notify_do_unsafe',
            'uses' => 'Payum\LaravelPackage\Controller\NotifyController@doUnsafeAction'
        ));
    }
}