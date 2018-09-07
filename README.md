## About

PHP\_CodeSniffer is a set of two PHP scripts; the main `phpcs` script that tokenizes PHP, JavaScript and CSS files to detect violations of a defined coding standard, and a second `phpcbf` script to automatically correct coding standard violations. PHP\_CodeSniffer is an essential development tool that ensures your code remains clean and consistent.

[![Build Status](https://travis-ci.org/squizlabs/PHP_CodeSniffer.svg?branch=phpcs-fixer)](https://travis-ci.org/squizlabs/PHP_CodeSniffer) [![Code consistency](http://squizlabs.github.io/PHP_CodeSniffer/analysis/squizlabs/PHP_CodeSniffer/grade.svg)](http://squizlabs.github.io/PHP_CodeSniffer/analysis/squizlabs/PHP_CodeSniffer) [![Join the chat at https://gitter.im/squizlabs/PHP_CodeSniffer](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/squizlabs/PHP_CodeSniffer?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Requirements

PHP\_CodeSniffer requires PHP version 5.4.0 or greater, although individual sniffs may have additional requirements such as external applications and scripts. See the [Configuration Options manual page](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Configuration-Options) for a list of these requirements.

## Installation

The easiest way to get started with PHP\_CodeSniffer is to download the Phar files for each of the commands:
```
# Download using curl
curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar

# Or download using wget
wget https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
wget https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar

# Then test the downloaded PHARs
php phpcs.phar -h
php phpcbf.phar -h
```

### Composer
If you use Composer, you can install PHP_CodeSniffer system-wide with the following command:

    composer global require "squizlabs/php_codesniffer=*"

Make sure you have the composer bin dir in your PATH. The default value is `~/.composer/vendor/bin/`, but you can check the value that you need to use by running `composer global config bin-dir --absolute`.

Or alternatively, include a dependency for `squizlabs/php_codesniffer` in your `composer.json` file. For example:

```json
{
    "require-dev": {
        "squizlabs/php_codesniffer": "3.*"
    }
}
```

You will then be able to run PHP_CodeSniffer from the vendor bin directory:

    ./vendor/bin/phpcs -h
    ./vendor/bin/phpcbf -h

### Phive
If you use Phive, you can install PHP_CodeSniffer as a project tool using the following commands:

    phive install phpcs
    phive install phpcbf

You will then be able to run PHP_CodeSniffer from the tools directory:

    ./tools/phpcs -h
    ./tools/phpcbf -h

### PEAR
If you use PEAR, you can install PHP\_CodeSniffer using the PEAR installer. This will make the `phpcs` and `phpcbf` commands immediately available for use. To install PHP\_CodeSniffer using the PEAR installer, first ensure you have [installed PEAR](http://pear.php.net/manual/en/installation.getting.php) and then run the following command:

    pear install PHP_CodeSniffer

### Git Clone
You can also download the PHP\_CodeSniffer source and run the `phpcs` and `phpcbf` commands directly from the Git clone:

    git clone https://github.com/squizlabs/PHP_CodeSniffer.git
    cd PHP_CodeSniffer
    php bin/phpcs -h
    php bin/phpcbf -h

## Documentation

The documentation for PHP\_CodeSniffer is available on the [Github wiki](https://github.com/squizlabs/PHP_CodeSniffer/wiki).

## Issues

Bug reports and feature requests can be submitted on the [Github Issue Tracker](https://github.com/squizlabs/PHP_CodeSniffer/issues).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for information.

## Versioning

PHP_CodeSniffer uses a `MAJOR.MINOR.PATCH` version number format.

The `MAJOR` version is incremented when:
- backwards-incompatible changes are made to how the `phpcs` or `phpcbf` commands are used, or
- backwards-incompatible changes are made to the `ruleset.xml` format, or
- backwards-incompatible changes are made to the API used by sniff developers, or
- custom PHP_CodeSniffer token types are removed

The `MINOR` version is incremented when:
- new backwards-compatible features are added to the `phpcs` and `phpcbf` commands, or
- backwards-compatible changes are made to the `ruleset.xml` format, or
- backwards-compatible changes are made to the API used by sniff developers, or
- new sniffs are added to an included standard

> NOTE: Backwards-compatible changes to the API used by sniff develpers will allow an existing sniff to continue running without producing fatal errors but may not result in the sniff reporting the same errors as it did previously without changes being required.

The `PATCH` version is incremented when:
- backwards-compatible bug fixes are made

> NOTE: As PHP_CodeSniffer exists to report and fix issues, most bugs are the result of coding standard errors being incorrectly reported or coding standard errors not being reported when they should be. This means that the messages produced by PHP_CodeSniffer, and the fixes it makes, are likely to be different between PATCH versions.
