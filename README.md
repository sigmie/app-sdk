# app-sdk

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```
bin/        
build/
docs/
config/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require sigmie/app-sdk
```

## Usage

``` php
$skeleton = new sigmie\app-sdk();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email nico@sigmie.com instead of using the issue tracker.

## Credits

- [Nico Orfanos][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/sigmie/app-sdk.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/sigmie/app-sdk/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/sigmie/app-sdk.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/sigmie/app-sdk.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/sigmie/app-sdk.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/sigmie/app-sdk
[link-travis]: https://travis-ci.org/sigmie/app-sdk
[link-scrutinizer]: https://scrutinizer-ci.com/g/sigmie/app-sdk/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/sigmie/app-sdk
[link-downloads]: https://packagist.org/packages/sigmie/app-sdk
[link-author]: https://github.com/nicoorfi
[link-contributors]: ../../contributors
