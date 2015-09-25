<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use Payum\Core\Bridge\Symfony\ReplyToSymfonyResponseConverter;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier;
use Payum\Core\PayumBuilder;
use Payum\Core\Registry\StorageRegistryInterface;
use Payum\Core\Storage\StorageInterface;
use Payum\LaravelPackage\Action\GetHttpRequestAction;
use Payum\LaravelPackage\Action\ObtainCreditCardAction;
use Payum\LaravelPackage\Security\TokenFactory;

class PayumServiceProvider extends ServiceProvider
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
        if (version_compare(Application::VERSION, '5.0', '<')) {
            $srcDir = realpath(__DIR__ . '/../../');

            $this->package('payum/payum-laravel-package', 'payum-laravel-package', $srcDir);
        }

        $this->defineRoutes();
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->registerServices();
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return [
            'payum',
            'payum.builder',
            'payum.converter.reply_to_http_response',
        ];
    }

    protected function registerServices()
    {
        $this->app->bind('payum.builder', function($app) {
            $builder = new PayumBuilder();
            $builder
                ->setTokenFactoryBuilder(function(StorageInterface $tokenStorage, StorageRegistryInterface $registry) {
                    return new TokenFactory($tokenStorage, $registry);
                })
                ->setHttpRequestVerifierBuilder(function(StorageInterface $tokenStorage) {
                    return new HttpRequestVerifier($tokenStorage);
                })
                ->setCoreGatewayFactoryBuilder(function(array $defaultConfig) {
                    $factory = new CoreGatewayFactory($defaultConfig);
                    $factory->setContainer($this->app);

                    return $factory;
                })
                ->setCoreGatewayFactoryConfig([
                    'payum.action.get_http_request' => 'payum.action.get_http_request',
                    'payum.action.obtain_credit_card' => 'payum.action.obtain_credit_card',
                ])
                ->setGenericTokenFactoryPaths([
                    'capture' => 'payum_capture_do',
                    'notify' => 'payum_notify_do',
                    'authorize' => 'payum_authorize_do',
                    'refund' => 'payum_refund_do',
                ])
            ;

            return $builder;
        });

        $this->app['payum'] = $this->app->share(function($app) {
            return $app['payum.builder']->getPayum();
        });

        $this->app['payum.converter.reply_to_http_response'] = $this->app->share(function($app) {
            return new ReplyToSymfonyResponseConverter();
        });

        $this->app['payum.action.get_http_request'] = $this->app->share(function($app) {
            return new GetHttpRequestAction();
        });

        $this->app['payum.action.obtain_credit_card'] = $this->app->share(function($app) {
            return new ObtainCreditCardAction();
        });
    }

    /**
     * Define all package routes with Laravel router
     */
    protected function defineRoutes()
    {
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