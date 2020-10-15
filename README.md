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

The driver for Savvy Gift Cards. It allows you to validate and redeemed a card, and reverse a previous redemption.

It supports making requests with and without a PIN.

## What's Not Included


## Basic Usage

For the driver in this repo, there are voucher-type requests, namely validate, redeem and unredeem, and there is a subset of the normal "Omnipay" requests, namely authorize, purchase and refund.

Use the voucher-type requests when you're treating the vouchers as vouchers; use the "Omnipay" requests when you are treating them as payments.

For general Omnipay usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you believe you have found a bug in this driver, please report it using the [GitHub issue tracker](https://github.com/digitickets/omnipay-savvy-gift-card/issues),
or better yet, fork the library and submit a pull request.
