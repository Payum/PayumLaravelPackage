<?php
namespace Payum\LaravelPackage\Controller;

use Illuminate\Routing\Controller;
use Payum\Core\Bridge\Symfony\ReplyToSymfonyResponseConverter;
use Payum\Core\Payum;
use Payum\Core\Reply\ReplyInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class PayumController extends Controller
{
    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return \App::make('payum');
    }

    /**
     * @param ReplyInterface $reply
     *
     * @return Response
     */
    protected function convertReply(ReplyInterface $reply)
    {
        /** @var ReplyToSymfonyResponseConverter $converter */
        $converter = \App::make('payum.converter.reply_to_http_response');

        return $converter->convert($reply);
    }
}