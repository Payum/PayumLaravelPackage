<?php
namespace Payum\LaravelPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Payum\Core\Bridge\Symfony\Reply\HttpResponse as SymfonyHttpResponse;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier;
use Payum\Core\Exception\LogicException;
use Payum\Core\Extension\StorageExtension;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Security\GenericTokenFactory;
use Payum\LaravelPackage\Action\GetHttpRequestAction;
use Payum\LaravelPackage\Action\ObtainCreditCardAction;
use Payum\LaravelPackage\CoreGatewayFactory;
use Payum\LaravelPackage\GatewayFactoriesProvider;
use Payum\LaravelPackage\Registry\ContainerAwareRegistry;
use Payum\LaravelPackage\Security\TokenFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Laravel4 extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $srcDir = realpath(__DIR__.'/../../../');


        $this->package('payum/payum-laravel-package', 'payum-laravel-package', $srcDir);
        \View::addNamespace('payum/payum', $srcDir.'/views');

        // Throw reply exceptions unless the config is set not to for Laravel 4 only
        if(\Config::get('payum-laravel-package::settings.throwReplyExceptions') == null) {
            \Config::set('payum-laravel-package::settings.throwReplyExceptions', true);
        }

        $this->app->error(function(ReplyInterface $reply)
        {
            $response = null;

            if ($reply instanceof SymfonyHttpResponse) {
                $response = $reply->getResponse();
            } elseif ($reply instanceof HttpResponse) {
                $response = new Response($reply->getContent());
            } elseif ($reply instanceof HttpRedirect) {
                $response = new RedirectResponse($reply->getUrl());
            }

            if ($response) {
                return $response;
            }

            $ro = new \ReflectionObject($reply);
            throw new LogicException(
                sprintf('Cannot convert reply %s to Laravel response.', $ro->getShortName()),
                null,
                $reply
            );
        });

        \Route::any('/payment/authorize/{payum_token}', array(
            'as' => 'payum_authorize_do',
            'uses' => 'Payum\LaravelPackage\Controller\AuthorizeController@doAction'
        ));

        \Route::any('/payment/capture/{payum_token}', array(
            'as' => 'payum_capture_do',
            'uses' => 'Payum\LaravelPackage\Controller\CaptureController@doAction'
        ));

        \Route::any('/payment/refund/{payum_token}', array(
            'as' => 'payum_refund_do',
            'uses' => 'Payum\LaravelPackage\Controller\RefundController@doAction'
        ));

        \Route::get('/payment/notify/{payum_token}', array(
            'as' => 'payum_notify_do',
            'uses' => 'Payum\LaravelPackage\Controller\NotifyController@doAction'
        ));

        \Route::get('/payment/notify/unsafe/{gateway_name}', array(
            'as' => 'payum_notify_do_unsafe',
            'uses' => 'Payum\LaravelPackage\Controller\NotifyController@doUnsafeAction'
        ));

        $this->app['payum'] = $this->app->share(function($app) {
            //TODO add exceptions if invalid gateways and storages options set.

            $payum = new ContainerAwareRegistry(
                \Config::get('payum-laravel-package::gateways'),
                \Config::get('payum-laravel-package::storages'),
                array_replace(
                    $app['payum.gateway_factories_provider']->provide(),
                    \Config::get('payum-laravel-package::factories') ?: []
                )
            );

            $payum->setContainer($app);

            return $payum;
        });

        $this->app['payum.security.token_storage'] = $this->app->share(function($app) {
            //TODO add exceptions if invalid gateways and storages options set.

            $tokenStorage = \Config::get('payum-laravel-package::token_storage');

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

        $this->app['payum.gateway_factories_provider'] = $this->app->share(function($app) {
            return new GatewayFactoriesProvider($app['payum.core_gateway']);
        });

        $this->app['payum.core_gateway'] = $this->app->share(function($app) {
            $config = [
                'payum.action.get_http_request' => 'payum.action.get_http_request',
                'payum.action.obtain_credit_card' => 'payum.action.obtain_credit_card',
            ];

            $storagesConfig = \Config::get('payum-laravel-package::storages');
            foreach ($storagesConfig as $modelClass => $storage) {
                $config['payum.extension.'.$modelClass] = new StorageExtension(is_object($storage) ? $storage : $app[$storage]);
            }

            $factory = new CoreGatewayFactory($config);
            $factory->setContainer($app);

            return $factory;
        });

        $this->app['payum.action.get_http_request'] = $this->app->share(function($app) {
            return new GetHttpRequestAction();
        });

        $this->app['payum.action.obtain_credit_card'] = $this->app->share(function($app) {
            return new ObtainCreditCardAction();
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