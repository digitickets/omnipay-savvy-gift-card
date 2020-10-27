<?php

namespace DigiTickets\Savvy\Messages;

use DigiTickets\Savvy\SavvyGateway;
use Omnipay\Common\Message\RequestInterface;

class RedeemRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        // The endpoints are different between using a PIN and not using one.
        return $this->getUsePIN() === true ? 'redeem' : 'redeemnopin';
    }

    public function getData()
    {
        $result = array_merge(
            $this->makeRequestContext(),
            [
                'cardNumber' => $this->getCardNumber(),
                'currency' => $this->determineCurrencyNumber(),
                'amount' => (float)$this->getAmount(), // API endpoint crashes if this is not a float!
            ]
        );
        // If the gateway is using PINs, add it to the data now.
        if ($this->getUsePIN() === true) {
            $result['pin'] = $this->getPin();
        }

        return $result;
    }

    public function sendData($data)
    {
ini_set('display_errors', 1);
//\DigiTickets\Applications\Commands\Personal\Debug::clearFile();
        $rawResponse = $this->sendMessage($data);
\DigiTickets\Applications\Commands\Personal\Debug::log('$rawResponse: '.var_export($rawResponse, true));
        // @TODO: Need to add the test for this - parameter "reverseOnInsufficientFunds".
        // @TODO: Comment it properly.
        if (property_exists($rawResponse, 'responseCode') &&
            property_exists($rawResponse, 'amount') &&
            property_exists($rawResponse, 'authCode') &&
            $rawResponse->responseCode === 30) {
\DigiTickets\Applications\Commands\Personal\Debug::log('Response code is 30');
            $requestParameters = $this->getParameters();
            unset($requestParameters['gateway']); // @TODO: Note that the gateway gets added by the refund method.
            // @TODO: Need to say we need to add these.
            $requestParameters['amount'] = $rawResponse->amount;
            $requestParameters['authCode'] = $rawResponse->authCode;
            $requestParameters['transactionReference'] = $rawResponse->cardNumber;

\DigiTickets\Applications\Commands\Personal\Debug::log('$this->getParameters(): '.var_export($requestParameters, true));
            /** @var SavvyGateway $gateway */
            $gateway = $this->getGateway();
            $unredeemRequest = $gateway->unredeem($requestParameters);
\DigiTickets\Applications\Commands\Personal\Debug::log('We have the unredeem request.. sending it');
            $unredeemResponse = $unredeemRequest->send();
\DigiTickets\Applications\Commands\Personal\Debug::log('After sending unredeem request');
\DigiTickets\Applications\Commands\Personal\Debug::log('$unredeemResponse was successful: '.var_export($unredeemResponse->isSuccessful(), true));
        }

        return $this->response = $this->buildResponse($this, $rawResponse, $this->getToken());
    }

    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new RedeemResponse($request, $response, $token);
    }
}
