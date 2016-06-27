# About

Symplify\PHP7_CodeSniffer is a set of 2 scripts:

- `phpcs` tokenizes PHP files to detect violations of a defined coding standard
- `phpcbf` automatically corrects coding standard violations.

This is essential development tool that ensures your code **remains clean and consistent**.

[![Build Status](https://img.shields.io/travis/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://travis-ci.org/Symplify/PHP7_CodeSniffer)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/php7_codesniffer.svg?style=flat-square)](https://packagist.org/packages/symplify/php7_codesniffer)
[![Latest stable](https://img.shields.io/packagist/v/symplify/php7_codesniffer.svg?style=flat-square)](https://packagist.org/packages/symplify/php7_codesniffer)


## Installation

Install via composer:

```json
composer require symplify/php7_codesniffer --dev
```

You will then be able to run PHP_CodeSniffer from the vendor bin directory:

```bash
./vendor/bin/phpcs -h
./vendor/bin/phpcbf -h
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for information.
