<?php
namespace Payum\LaravelPackage\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Symfony\Request\ResponseInteractiveRequest;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\CreditCard;
use Payum\Core\Request\ObtainCreditCardRequest;
use Symfony\Component\HttpFoundation\Response;

class ObtainCreditCardAction implements ActionInterface
{

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request ObtainCreditCardRequest */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        if (\Request::isMethod('POST')) {
            $creditCard = new CreditCard;
            $creditCard->setHolder(\Input::get('card_holder'));
            $creditCard->setNumber(\Input::get('card_number'));
            $creditCard->setSecurityCode(\Input::get('card_cvv'));
            $creditCard->setExpireAt(new \DateTime(\Input::get('card_expire_at')));

            $request->set($creditCard);

            return;
        }

        $content = <<<HTML
<!DOCTYPE html>
<html>
<body>

<form method="POST">

<p>
    <label>Holder: </label>
    <input name="card_holder" value="" />
</p>
<p>
    <label>Number: </label>
    <input name="card_number" value="" />
</p>
<p>
    <label>Cvv: </label>
    <input name="card_cvv" value="" />
</p>
<p>
   <label>Expire at: </label>
    <input name="card_expire_at" value="" placeholder="yyyy-mm-dd"/>
</p>

<input type="submit" value="Submit" />
</form>

</body>
</html>
HTML;

        throw new ResponseInteractiveRequest(new Response($content, 200, array(
            'Cache-Control' => 'no-store, no-cache, max-age=0, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
        )));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof ObtainCreditCardRequest;
    }
}