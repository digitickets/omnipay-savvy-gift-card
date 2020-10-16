<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\AbstractRequest;

abstract class AbstractSavvyRequest extends AbstractRequest
{
    public function getUrl()
    {
        // @TODO: We must provide only the stem of the URL, without the "balance" on the end, and then add the
        // @TODO: specific endpoint inside the request.
        return $this->getTestMode()
            ?
            'https://api.savvyconnectdirect.net/sandbox/api/v1/balance'
            :
            'https://api.savvyconnectdirect.net/api/v1/balance';
    }

    protected function generateGuid()
    {
        // Found this code online. Is there an alternative already in the system? Should this be made global?
        // @TODO: Maybe tailor this for Savvy - "REQ_YYYYMMDD_<1st 8 chars of $charId>"?
        $charId = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid =
            substr($charId, 0, 8).$hyphen.
            substr($charId, 8, 4).$hyphen.
            substr($charId, 12, 4).$hyphen.
            substr($charId, 16, 4).$hyphen.
            substr($charId, 20, 12);

        return $uuid;
    }

    public function setAdminTeamId($value) {
        $this->setParameter('adminTeamId', $value);
    }

    protected function getAdminTeamId() {
        return $this->getParameter('adminTeamId');
    }

    public function setMerchantId($value) {
        $this->setParameter('merchantId', $value);
    }

    protected function getMerchantId() {
        return $this->getParameter('merchantId');
    }

    public function setConnectDirectSubscriptionKey($value) {
        $this->setParameter('connectDirectSubscriptionKey', $value);
    }

    protected function getConnectDirectSubscriptionKey() {
        return $this->getParameter('connectDirectSubscriptionKey');
    }

    /**
     * This is just a wrapper around setCardNumber() in case an application passes the card number in as "voucherCode".
     *
     * @param $value
     */
    public function setVoucherCode($value) {
        $this->setCardNumber($value);
    }

    public function setCardNumber($value) {
        $this->setParameter('cardNumber', $value);
    }

    protected function getCardNumber() {
        return $this->getParameter('cardNumber');
    }

    /**
     * This is just a wrapper around setPin() in case an application passes the PIN in as "voucherPin".
     *
     * @param $value
     */
    public function setVoucherPin($value) {
        $this->setPin($value);
    }

    public function setPin($value) {
        $this->setParameter('pin', $value);
    }

    protected function getPin() {
        return $this->getParameter('pin');
    }

    public function getData()
    {
        // @TODO: Implement getData() method.
    }

    public function sendData($data)
    {
        // @TODO: Implement sendData() method.
    }
}
