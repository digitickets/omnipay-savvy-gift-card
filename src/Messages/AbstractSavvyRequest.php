<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\AbstractRequest;

abstract class AbstractSavvyRequest extends AbstractRequest
{
    abstract protected function getEndpoint();

    public function getUrlStem()
    {
        return $this->getTestMode()
            ?
            'https://api.savvyconnectdirect.net/sandbox/api/v1'
            :
            'https://api.savvyconnectdirect.net/api/v1';
    }

    public function getUrl()
    {
        return sprintf('%s/%s', $this->getUrlStem(), $this->getEndpoint());
    }

    protected function buildHeaders()
    {
        // @TODO: We need to handle the logout having expired (or not existing at all).
        $bearer = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkNvbm5lY3REaXJlY3RUb2tlbiIsIk1lcmNoYW50SWQiOiJEWjIwMTkwOTA3dCIsIkFjcXVpcmVySWQiOiIxIiwibmJmIjoxNjAyODQ4MjA4LCJleHAiOjE2MDI4NTAwMDgsImlhdCI6MTYwMjg0ODIwOCwiaXNzIjoiU2F2dnlDb25uZWN0RGlyZWN0IiwiYXVkIjoiU2F2dnlDb25uZWN0RGlyZWN0In0.H2PNu9ccPzVX7Y9Em3StfHabs2YGUVYfcNN-ErIOPhAD46xneMN4RJc0WaI21lLBG3S9yc5RcO2goupVEgYe4Cb2DO2r1XcS9lLGZ0lkeyBcOx0vOI3HscuHjZvPVdLzD2raNOJ2TDevXiKS8GmNjyHeq7imTBWoEUTXVTUY611lbgifgnAH8H7ovWk7Rh1OgCJ68XPQ6FXNZ0aHE5A3DqyYkMnVIEYRRczJo_rsZ8gR6e78Q5W8igWdN-05BvGj-8SsrYgLjtj6-ND1srjkUrEGwLJrnTqk4G3muCixbi-aZfV4wPKRvSaMOubFVcoGcUSvElfhh2M-bGHCXDBaWQ';

        return
            [
                'Connect-Direct-Subscription-Key' => $this->getConnectDirectSubscriptionKey(),
                'Authorization' => sprintf('Bearer %s', $bearer),
                'Content-Type' => 'application/json',
            ];
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
