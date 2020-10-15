<?php

namespace DigiTickets\Savvy;

use DigiTickets\OmnipayAbstractVoucher\AbstractVoucherGateway;
use DigiTickets\Savvy\Messages\AuthorizeRequest;
use DigiTickets\Savvy\Messages\PurchaseRequest;
use DigiTickets\Savvy\Messages\RedeemRequest;
use DigiTickets\Savvy\Messages\RefundRequest;
use DigiTickets\Savvy\Messages\UnredeemRequest;
use DigiTickets\Savvy\Messages\ValidateRequest;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;

/**
 * Class SavvyGateway
 *
 * @method RequestInterface completeAuthorize(array $options = array()) (Optional method)
 *         Handle return from off-site gateways after authorization
 * @method RequestInterface capture(array $options = array())           (Optional method)
 *         Capture an amount you have previously authorized
 * @method RequestInterface completePurchase(array $options = array())  (Optional method)
 *         Handle return from off-site gateways after purchase
 * @method RequestInterface void(array $options = array())              (Optional method)
 *         Generally can only be called up to 24 hours after submitting a transaction
 * @method RequestInterface createCard(array $options = array())        (Optional method)
 *         The returned response object includes a cardReference, which can be used for future transactions
 * @method RequestInterface updateCard(array $options = array())        (Optional method)
 *         Update a stored card
 * @method RequestInterface deleteCard(array $options = array())        (Optional method)
 *         Delete a stored card
 */
class SavvyGateway extends AbstractVoucherGateway
{
    public function getName()
    {
        return 'Savvy Gift Card';
    }

    /**
     * This is just a wrapper around the parent createRequest() and just adds an instance of this gateway to the parameters.
     *
     * @param string $class
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    protected function createRequest($class, array $parameters)
    {
        $parameters['gateway'] = $this;

        return parent::createRequest($class, $parameters);
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest(AuthorizeRequest::class, $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    public function refund(array $parameters = array())
    {
        $parameters['unredeemRequest'] = $this->unredeem($parameters);
        return $this->createRequest(RefundRequest::class, $parameters);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest
     */
    public function validate(array $parameters = array())
    {
        return $this->createRequest(ValidateRequest::class, $parameters);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest
     */
    public function redeem(array $parameters = array())
    {
        return $this->createRequest(RedeemRequest::class, $parameters);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest
     */
    public function unredeem(array $parameters = array())
    {
        return $this->createRequest(UnredeemRequest::class, $parameters);
    }
}
