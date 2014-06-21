<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier;
use Payum\Core\Bridge\Symfony\Security\TokenFactory;
use Payum\LaravelPackage\Registry\ContainerAwareRegistry;

class PayumServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->package('payum/payum');

        \Route::get('/payment/capture/{payum_token}', array(
            'as' => 'payum_capture_do',
            'uses' => 'payum/payum::CaptureController@do'
        ));

        \Route::get('/payment/notify/{payum_token}', array(
            'as' => 'payum_notify_do',
            'uses' => 'payum/payum::NotifyController@do'
        ));

        \Route::get('/payment/notify/unsafe/{payment_name}', array(
            'as' => 'payum_notify_do_unsafe',
            'uses' => 'payum/payum::NotifyController@doUnsafe'
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app['payum'] = $this->app->share(function($app) {
            $payum = new ContainerAwareRegistry(
                $app['config']['payum/payum::payments'],
                $app['config']['payum/payum::storages']
            );

            $payum->setContainer($app);

            return $payum;
        });

        $this->app['payum.security.token_storage'] = $this->app->share(function($app) {
            $tokenStorage = $app['config']['payum/payum::token_storage'];

            return is_object($tokenStorage) ? $tokenStorage : $app[$tokenStorage];
        });

        $this->app['payum.security.token_factory'] = $this->app->share(function($app) {
            return new TokenFactory(
                $app['router'],
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