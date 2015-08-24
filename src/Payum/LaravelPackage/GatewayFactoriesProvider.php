<?php
namespace Payum\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use Payum\AuthorizeNet\Aim\AuthorizeNetAimGatewayFactory;
use Payum\Be2Bill\Be2BillDirectGatewayFactory;
use Payum\Be2Bill\Be2BillOffsiteGatewayFactory;
use Payum\Core\GatewayFactoryInterface;
use Payum\Klarna\Checkout\KlarnaCheckoutGatewayFactory;
use Payum\Klarna\Invoice\KlarnaInvoiceGatewayFactory;
use Payum\Offline\OfflineGatewayFactory;
use Payum\OmnipayBridge\OmnipayDirectGatewayFactory;
use Payum\OmnipayBridge\OmnipayOffsiteGatewayFactory;
use Payum\Payex\PayexGatewayFactory;
use Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutGatewayFactory;
use Payum\Paypal\ProCheckout\Nvp\PaypalProCheckoutGatewayFactory;
use Payum\Paypal\Rest\PaypalRestGatewayFactory;
use Payum\Stripe\StripeCheckoutGatewayFactory;
use Payum\Stripe\StripeJsGatewayFactory;

class GatewayFactoriesProvider
{
    /**
     * @var GatewayFactoryInterface
     */
    private $coreGatewayFactory;

    /**
     * @param GatewayFactoryInterface $coreGatewayFactory
     */
    public function __construct(GatewayFactoryInterface $coreGatewayFactory)
    {
        $this->coreGatewayFactory = $coreGatewayFactory;
    }

    /**
     * @return GatewayFactoryInterface[]
     */
    public function provide()
    {
        $factories = [];

        if (class_exists(PaypalExpressCheckoutGatewayFactory::class)) {
            $factories['paypal_express_checkout'] = new PaypalExpressCheckoutGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(PaypalProCheckoutGatewayFactory::class)) {
            $factories['paypal_pro_checkout'] = new PaypalProCheckoutGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(PaypalRestGatewayFactory::class)) {
            $factories['paypal_rest'] = new PaypalRestGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(StripeJsGatewayFactory::class)) {
            $factories['stripe_js'] = new StripeJsGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(StripeCheckoutGatewayFactory::class)) {
            $factories['stripe_checkout'] = new StripeCheckoutGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(AuthorizeNetAimGatewayFactory::class)) {
            $factories['authoirze_net'] = new AuthorizeNetAimGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(Be2BillDirectGatewayFactory::class)) {
            $factories['be2bill_direct'] = new Be2BillDirectGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(Be2BillOffsiteGatewayFactory::class)) {
            $factories['be2bill_offsite'] = new Be2BillOffsiteGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(KlarnaCheckoutGatewayFactory::class)) {
            $factories['klarna_checkout'] = new KlarnaCheckoutGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(KlarnaInvoiceGatewayFactory::class)) {
            $factories['klarna_invoice'] = new KlarnaInvoiceGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(OfflineGatewayFactory::class)) {
            $factories['offline'] = new OfflineGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(PayexGatewayFactory::class)) {
            $factories['payex'] = new PayexGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(OmnipayDirectGatewayFactory::class)) {
            $factories['omnipay_direct'] = new OmnipayDirectGatewayFactory([], $this->coreGatewayFactory);
        }
        if (class_exists(OmnipayOffsiteGatewayFactory::class)) {
            $factories['omnipay_offsite'] = new OmnipayOffsiteGatewayFactory([], $this->coreGatewayFactory);
        }

        return $factories;
    }
}