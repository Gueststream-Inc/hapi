# HomeAway Payment Island API Wrapper #

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is a PHP library to make it easy to implement the HAPI HAT (HomeAway Payment Island Alternate Tokenization) service
provided by HomeAway to remove HomeAway and any other data or service provider from the PCI scope except the service
that is actively accepting credit card data from the guest.  More information can be found on the 
[HAPI Reference Page](https://beta.escapia.com/HAPI_Reference/index.html).


## Install

Via Composer

``` bash
$ composer require gueststream/hapi
```

## Usage
Invoke the Tokenizer using the HAT Client ID and API Key provided by HomeAway, then pass a credit card number to the `tokenize()` method to request and receive the token for that credit card number.

``` php
$hatClientId = "a7209c11-32e5-4c55-bff5-617607d85e8f";
$hatApiKey = "c5de60cfccd04f84a502bfb9c63d2f28";
$creditCardNumber = "4111111111111111";

$tokenizer = new Gueststream\HomeAway\Tokenizer($hatClientId, $hatApiKey);
$tokenizedCardNumber = $tokenizer->tokenize($creditCardNumber);
```

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email support@gueststream.com instead of using the issue tracker.

## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/gueststream/hapi.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Gueststream-Inc/hapi/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Gueststream-Inc/hapi.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Gueststream-Inc/hapi.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/gueststream/hapi.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/gueststream/hapi
[link-travis]: https://travis-ci.org/Gueststream-Inc/hapi
[link-scrutinizer]: https://scrutinizer-ci.com/g/Gueststream-Inc/hapi/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Gueststream-Inc/hapi
[link-downloads]: https://packagist.org/packages/gueststream/hapi
[link-contributors]: ../../contributors
