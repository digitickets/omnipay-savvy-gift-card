# omnipay-savvy-gift-card

**Savvy Gift Card redemptions driver for the Omnipay PHP payment processing library**

Omnipay implementation of Savvy Gift Card redemption. Obviously it's not a payment gateway, but it behaves in a similar way.

See [their technical documentation](https://developer.savvyconnectdirect.net/) for more details.

[![Build Status](https://travis-ci.org/digitickets/omnipay-savvy-gift-card.png?branch=master)](https://travis-ci.org/digitickets/omnipay-savvy-gift-card)
[![Latest Stable Version](https://poser.pugx.org/digitickets/omnipay-savvy-gift-card/version.png)](https://packagist.org/packages/omnipay/savvy-gift-card)
[![Total Downloads](https://poser.pugx.org/digitickets/omnipay-savvy-gift-card/d/total.png)](https://packagist.org/packages/digitickets/omnipay-savvy-gift-card)

## Installation

**Important: Driver requires [PHP's Intl extension](http://php.net/manual/en/book.intl.php) to be installed.**

The Savvy Gift Card Omnipay driver is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "digitickets/omnipay-savvy-gift-card": "^1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## What's Included

The driver for Savvy Gift Cards. It allows you to validate and redeem a card, and reverse a previous redemption.

It supports making requests with and without a PIN.

It can automatically revert a redemption where there were insufficient funds.

## What's Not Included

This driver does not handle any of the other card management operations, such as loading a card, unfreezing, unloading, doing an ad hoc refund, etc.

## Basic Usage

For the driver in this repo, there are voucher-type requests, namely validate, redeem and unredeem, and there is a subset of the normal "Omnipay" requests, namely authorize, purchase and refund.

Use the voucher-type requests when you're treating the vouchers as vouchers; use the "Omnipay" requests when you are treating them as payments.

For general Omnipay usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

### Running with/without a PIN

By default, the driver assumes that you are using PINs; it will call the endpoints that require a PIN.

There is a parameter, ```usePIN```. If true, it assumes you are using PINs. If false, it assumes you are not using PINs and will call the "*nopin" endpoints where necessary. It defaults to true.

### Handling redemptions with insufficient funds

If you attempt to redeem a gift card where the redemption amount is greater than the current balance on the card, the API will reduce the current balance to zero and return a response code of "30".

This is not ideal as money has been taken off the card, but the API returns an error.

There is therefore a parameter, ```failOnInsufficientFunds``` to say what to do. If true, it will immediately revert the redemption (so the card ends up with the balance that it started with) and will return the original, error, response.

If set to false, it will simply adjust the response to have a response code of "0" and an amount equal to the actual amount taken off the card, and do nothing else. In this case, it's up to the merchant to compare the requested amount with the response amount to detect if there were insufficient funds.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you believe you have found a bug in this driver, please report it using the [GitHub issue tracker](https://github.com/digitickets/omnipay-savvy-gift-card/issues),
or better yet, fork the library and submit a pull request.
