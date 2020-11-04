<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;

abstract class AbstractSavvyRequest extends AbstractRequest
{
    abstract protected function getEndpoint();

    /**
     * This method is here because several request classes subclass other request classes, and each one needs to
     * return an instance of the correct response class.
     *
     * @param RequestInterface $request
     * @param $response
     * @param string|null $token
     *
     * @return AbstractSavvyResponse
     */
    abstract protected function buildResponse(RequestInterface $request, $response, string $token = null);

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
        if (property_exists($rawResponse, 'authenticated') &&
            property_exists($rawResponse, 'token') &&
            $rawResponse->authenticated === true) {
            $this->setToken($rawResponse->token);
        }
    }

    /**
     * This method returns the data values that identify the merchant and the request.
     *
     * @return array
     */
    protected function makeRequestContext()
    {
        return [
            'requestId' => $this->generateGuid(),
            'adminTeamId' => $this->getAdminTeamId(),
            'merchantId' => $this->getMerchantId(),
        ];
    }

    /**
     * We have to extract this out because we may have an auth token, but it might have expired. If that's the case,
     * we need to try _once_ to login and re-send the message, hence needing the $firstAttempt parameter. It also
     * means this logic is in one place, rather than in each request class.
     *
     * @param $data
     * @param bool $firstAttempt
     *
     * @return mixed
     */
    protected function sendMessage($data, bool $firstAttempt = true)
    {
        try {
            $responseBody = $this->httpClient->post(
                $this->getUrl(),
                $this->buildHeaders(),
                json_encode($data)
            )
                ->send()
                ->getBody();
            $rawResponse = json_decode($responseBody); // Decode to stdClass
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // If this is the first attempt to send the message and the exception was because the auth token is
            // invalid, we need to explicitly log in (thereby refreshing the token) and try again.
            // It's hard to know how to reliably test for that specific error. We can't test the status code because
            // it throws an exception without instantiating the response object.
            if ($firstAttempt && strpos($message, '401') !== false && strpos($message, 'Unauthorized') !== false) {
                $this->login();
                $rawResponse = $this->sendMessage($data, false);
            } else {
                throw $e;
            }
        }

        return $rawResponse;
    }

    public function setGateway($value)
    {
        $this->setParameter('gateway', $value);
    }

    public function getGateway()
    {
        return $this->getParameter('gateway');
    }

    protected function generateGuid()
    {
        // Found this code online. Is there an alternative already in the system? Should this be made global?
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

    /**
     * The Savvy API requires the currency type as a number, eg "978" for Euros. However, the Omnipay
     * standard is to pass in the currency code, eg "EUR" for Euros, so we have to cope with the code
     * being present.
     * So, we look at the currency value in the object, and if it looks like a code, convert it to the
     * number; otherwise leave it as it is. Therefore 'GBP' maps to '826', '978' maps to '978', 'AED' maps to 'AED'.
     * Note also that the currency numbers have to be a string.
     *
     * @return int
     */
    public function determineCurrencyNumber()
    {
        $map = [
            'GBP' => '826',
            'EUR' => '978',
            'USD' => '840',
        ];
        $currencyCode = $this->getCurrency();

        return $map[$currencyCode] ?? $currencyCode;
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

    public function setUsePIN($value) {
        $this->setParameter('usePIN', $value);
    }

    protected function getUsePIN() {
        return $this->getParameter('usePIN');
    }

    public function setFailOnInsufficientFunds($value) {
        $this->setParameter('failOnInsufficientFunds', $value);
    }

    protected function getFailOnInsufficientFunds() {
        return $this->getParameter('failOnInsufficientFunds');
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

    public function setAuthCode($value) {
        $this->setParameter('authCode', $value);
    }

    protected function getAuthCode() {
        return $this->getParameter('authCode');
    }
}
