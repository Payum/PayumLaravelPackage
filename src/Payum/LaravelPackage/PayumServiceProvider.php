<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier;
use Payum\LaravelPackage\Registry\ContainerAwareRegistry;
use Payum\LaravelPackage\Security\TokenFactory;

class PayumServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->package('payum/payum-laravel-package');

        \Route::get('/payment/capture/{payum_token}', array(
            'as' => 'payum_capture_do',
            'uses' => 'Payum\LaravelPackage\Controller\CaptureController@doAction'
        ));

        \Route::get('/payment/notify/{payum_token}', array(
            'as' => 'payum_notify_do',
            'uses' => 'Payum\LaravelPackage\Controller\NotifyController@doAction'
        ));

        \Route::get('/payment/notify/unsafe/{payment_name}', array(
            'as' => 'payum_notify_do_unsafe',
            'uses' => 'Payum\LaravelPackage\Controller\NotifyController@doUnsafeAction'
        ));

        $this->app['payum'] = $this->app->share(function($app) {
            //TODO add exceptions if invalid payments and storages options set.

            $payum = new ContainerAwareRegistry(
                \Config::get('payum-laravel-package::payments'),
                \Config::get('payum-laravel-package::storages')
            );

            $payum->setContainer($app);

            return $payum;
        });

        $this->app['payum.security.token_storage'] = $this->app->share(function($app) {
            //TODO add exceptions if invalid payments and storages options set.

            $tokenStorage = \Config::get('payum-laravel-package::token_storage');

            return is_object($tokenStorage) ? $tokenStorage : $app[$tokenStorage];
        });

        $this->app['payum.security.token_factory'] = $this->app->share(function($app) {
            return new TokenFactory(
                $app['payum.security.token_storage'],
                $app['payum'],
                'payum_capture_do',
                'payum_notify_do'
            );
        });

        $this->app['payum.security.http_request_verifier'] = $this->app->share(function($app) {
            return new HttpRequestVerifier($app['payum.security.token_storage']);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
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
}