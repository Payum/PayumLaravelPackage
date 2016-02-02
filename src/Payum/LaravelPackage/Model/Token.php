<?php
namespace Payum\LaravelPackage\Model;

use Illuminate\Database\Eloquent\Model;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\Random;

class Token extends Model implements TokenInterface
{
    /**
     * @var string
     */
    protected $table = 'payum_tokens';

    /**
     * @var string
     */
    protected $primaryKey = 'hash';
    
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    protected static $unguarded = true;

    public function __construct(array $attributes = array())
    {
        if (empty($attributes['hash'])) {
            $attributes['hash'] = Random::generateToken();
        }

        parent::__construct($attributes);
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
     * {@inheritDoc}
     */
    public function setDetails($details)
    {
        $this->setAttribute('details', serialize($details));
    }

    /**
     * {@inheritDoc}
     */
    public function getDetails()
    {
        return unserialize($this->getAttribute('details'));
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
    public function getGatewayName()
    {
        return $this->getAttribute('gatewayName');
    }

    /**
     * @param string $gatewayName
     */
    public function setGatewayName($gatewayName)
    {
        $this->setAttribute('gatewayName', $gatewayName);
    }
}
