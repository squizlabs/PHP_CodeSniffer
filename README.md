About
-----

PHP\_CodeSniffer is a set of two PHP scripts; the main `phpcs` script that tokenizes PHP, JavaScript and CSS files to detect violations of a defined coding standard, and a second `phpcbf` script to automatically correct coding standard violations. PHP\_CodeSniffer is an essential development tool that ensures your code remains clean and consistent.

[![Build Status](https://img.shields.io/travis/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://travis-ci.org/Symplify/PHP7_CodeSniffer)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)

[![Join the chat at https://gitter.im/squizlabs/PHP_CodeSniffer](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/squizlabs/PHP_CodeSniffer?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)


Installation
------------

If you prefer using [Composer](http://getcomposer.org/) you can easily install PHP_CodeSniffer system-wide with the following command:

```json
composer global require squizlabs/php_codesniffer
```

Or alternatively locally:

```json
composer require squizlabs/php_codesniffer --dev
```

You will then be able to run PHP_CodeSniffer from the vendor bin directory:

```bash
./vendor/bin/phpcs -h
./vendor/bin/phpcbf -h
```

Documentation
-------------

The documentation for PHP\_CodeSniffer is available on the [Github wiki](https://github.com/squizlabs/PHP_CodeSniffer/wiki).

Information about upcoming features and releases is available on the [Squiz Labs blog](http://www.squizlabs.com/php-codesniffer).

Issues
------

Bug reports and feature requests can be submitted on the [Github Issue Tracker](https://github.com/squizlabs/PHP_CodeSniffer/issues) or the [PEAR bug tracker](http://pear.php.net/package/PHP_CodeSniffer/bugs).

Contributing
-------------

See [CONTRIBUTING.md](CONTRIBUTING.md) for information.
