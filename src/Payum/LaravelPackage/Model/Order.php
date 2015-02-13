<?php
namespace Payum\LaravelPackage\Model;

use Illuminate\Database\Eloquent\Model;
use Payum\Core\Model\OrderInterface;

class Order extends Model implements  OrderInterface
{
    protected $table = 'payum_orders';

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
        return json_decode($this->getAttribute('details') ?: '[]', true);
    }

    /**
     * {@inheritDoc}
     */
    public function getNumber()
    {
        return $this->getAttribute('number');
    }

    /**
     * {@inheritDoc}
     */
    public function setNumber($number)
    {
        $this->setAttribute('number', $number);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->getAttribute('description');
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {
        $this->setAttribute('description', $description);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientEmail()
    {
        // TODO: Implement getClientEmail() method.
    }

    /**
     * {@inheritDoc}
     */
    public function setClientEmail($clientEmail)
    {
        $this->setAttribute('clientEmail', $clientEmail);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientId()
    {
        return $this->getAttribute('clientId');
    }

    /**
     * {@inheritDoc}
     */
    public function setClientId($clientId)
    {
        $this->setAttribute('clientId', $clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function getTotalAmount()
    {
        return $this->getAttribute('totalAmount');
    }

    /**
     * {@inheritDoc}
     */
    public function setTotalAmount($totalAmount)
    {
        $this->setAttribute('totalAmount', $totalAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyCode()
    {
        return $this->getAttribute('currencyCode');
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->setAttribute('currencyCode', $currencyCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyDigitsAfterDecimalPoint()
    {
        return $this->getAttribute('currencyDigitsAfterDecimalPoint');
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrencyDigitsAfterDecimalPoint($currencyDigitsAfterDecimalPoint)
    {
        $this->setAttribute('currencyDigitsAfterDecimalPoint', $currencyDigitsAfterDecimalPoint);
    }
}