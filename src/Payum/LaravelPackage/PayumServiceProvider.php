<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Payum\Core\Bridge\Symfony\Request\ResponseInteractiveRequest as SymfonyResponseInteractiveRequest;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier;
use Payum\Core\Exception\LogicException;
use Payum\Core\Request\InteractiveRequestInterface;
use Payum\Core\Request\Http\RedirectUrlInteractiveRequest;
use Payum\Core\Request\Http\ResponseInteractiveRequest;
use Payum\LaravelPackage\Registry\ContainerAwareRegistry;
use Payum\LaravelPackage\Security\TokenFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PayumServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->package('payum/payum-laravel-package');
        \View::addNamespace('payum/payum', __DIR__.'/../../views');

        $this->app->error(function(InteractiveRequestInterface $interactiveRequest)
        {
            $response = null;

            if ($interactiveRequest instanceof SymfonyResponseInteractiveRequest) {
                $response = $interactiveRequest->getResponse();
            } elseif ($interactiveRequest instanceof ResponseInteractiveRequest) {
                $response = new Response($interactiveRequest->getContent());
            } elseif ($interactiveRequest instanceof RedirectUrlInteractiveRequest) {
                $response = new RedirectResponse($interactiveRequest->getUrl());
            }

            if ($response) {
                return $response;
            }

            $ro = new \ReflectionObject($interactiveRequest);
            throw new LogicException(
                sprintf('Cannot convert interactive request %s to symfony response.', $ro->getShortName()),
                null,
                $interactiveRequest
            );
        });

        \Route::any('/payment/capture/{payum_token}', array(
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