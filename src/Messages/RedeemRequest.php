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
        $rawResponse = $this->sendMessage($data);
        if (property_exists($rawResponse, 'responseCode') &&
            property_exists($rawResponse, 'amount') &&
            property_exists($rawResponse, 'authCode') &&
            $rawResponse->responseCode === 30) {
            // There weren't sufficient funds on the card, but the gateway has taken everything off that it can. If the
            // merchant says this is not good, then we revert the transaction and treat it as an error.
            // If the merchant is okay with it, we treat it as successful, but build the response in such a way that
            // it's possible to tell what happened (including exposing the actual amount of money that was taken off).
            if ($this->getFailOnInsufficientFunds()) {
                $requestParameters = $this->getParameters();
                unset($requestParameters['gateway']); // The gateway object gets added by the unredeem request.
                // There are a few values that are needed by the unredeem request but are not present in the original
                // response, so we need to add them here.
                $requestParameters['amount'] = $rawResponse->amount;
                $requestParameters['authCode'] = $rawResponse->authCode;
                $requestParameters['transactionReference'] = $rawResponse->cardNumber;

                // We do a "real" unredeem request, as if it was the merchant doing it.
                // We don't handle any errors here because it would just get too complicated.
                /** @var SavvyGateway $gateway */
                $gateway = $this->getGateway();
                $unredeemRequest = $gateway->unredeem($requestParameters);
                $unredeemRequest->send();
            } else {
                // Just pretend it was successful. The response object exposes the actual amount taken off.
                $rawResponse->responseCode = 0;
            }
        }

        return $this->response = $this->buildResponse($this, $rawResponse, $this->getToken());
    }

    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new RedeemResponse($request, $response, $token);
    }
}
