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

    protected function buildHeaders(bool $includeToken = true)
    {
        $headers = [
            'Connect-Direct-Subscription-Key' => $this->getConnectDirectSubscriptionKey(),
            'Content-Type' => 'application/json',
        ];

        // If the calling code needs the "session" token, make sure we've got it, then add it to the parameters.
        if ($includeToken) {
            // We need the token from the last login (which might have expired, of course). If there isn't one at all,
            // we definitely have to log in again. If there is one, we try it and if the response is "token has
            // expired", we log in and try the request again.
            $token = $this->getToken();
            if (is_null($token)) {
                // Log in and get a new token.
                $this->login();
                $token = $this->getToken();
            }
            $headers['Authorization'] = sprintf('Bearer %s', $token);
        }

        return $headers;
    }

    /**
     * @TODO: We need to make this a bit nicer, but leave it for now.
     */
    protected function login()
    {
        // Make the login ("get token") request.
        $responseBody = $this->httpClient->post(
            sprintf('%s/auth/get-token', $this->getUrlStem()),
            $this->buildHeaders(false),
            json_encode(
                [
                    'adminTeamId' => $this->getAdminTeamId(),
                    'merchantId' => $this->getMerchantId(),
                    'password' => $this->getPassword(),
                ]
            )
        )
        ->send()
        ->getBody();
        $rawResponse = json_decode($responseBody); // Decode to stdClass
        // Get the token out of the response.
        // @TODO: We need to handle errors, somehow.
        if ($rawResponse->authenticated === true) {
            $this->setToken($rawResponse->token);
        }
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

    public function setPassword($value) {
        $this->setParameter('password', $value);
    }

    protected function getPassword() {
        return $this->getParameter('password');
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
