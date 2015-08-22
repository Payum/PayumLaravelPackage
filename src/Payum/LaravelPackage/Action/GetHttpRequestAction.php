<?php
namespace Payum\LaravelPackage\Action;

use Payum\Core\Bridge\Symfony\Action\GetHttpRequestAction as SymfonyGetHttpRequestAction;

class GetHttpRequestAction extends SymfonyGetHttpRequestAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        $this->setHttpRequest(\App::make('request'));

        parent::execute($request);
    }

} 