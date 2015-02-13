<?php
namespace Payum\LaravelPackage\Model;

use Illuminate\Database\Eloquent\Model;
use Payum\Core\Security\TokenInterface;

class Toke extends Model implements TokenInterface
{
    protected $table = 'payum_tokens';

    /**
     * {@inheritDoc}
     */
    public function setDetails($details)
    {
        $this->setAttribute('details', json_encode($details));
    }

    /**
     * {@inheritDoc}
     */
    public function getDetails()
    {
        return json_decode($this->getAttribute('details') ?: '[]');
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->getAttribute('hash');
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->setAttribute('hash', $hash);
    }

    /**
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->getAttribute('targetUrl');
    }

    /**
     * @param string $targetUrl
     */
    public function setTargetUrl($targetUrl)
    {
        $this->setAttribute('targetUrl', $targetUrl);
    }

    /**
     * @return string
     */
    public function getAfterUrl()
    {
        return $this->getAttribute('afterUrl');
    }

    /**
     * @param string $afterUrl
     */
    public function setAfterUrl($afterUrl)
    {
        $this->setAttribute('afterUrl', $afterUrl);
    }

    /**
     * @return string
     */
    public function getPaymentName()
    {
        return $this->getAttribute('paymentName');
    }

    /**
     * @param string $paymentName
     */
    public function setPaymentName($paymentName)
    {
        $this->setAttribute('paymentName', $paymentName);
    }
}