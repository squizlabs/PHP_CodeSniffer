About
-----

PHP\_CodeSniffer is a PHP5 script that tokenises PHP, JavaScript and CSS files to detect violations of a defined coding standard. It is an essential development tool that ensures your code remains clean and consistent. It can also help prevent some common semantic errors made by developers.

[![Build Status](https://travis-ci.org/squizlabs/PHP_CodeSniffer.svg?branch=master)](https://travis-ci.org/squizlabs/PHP_CodeSniffer) [![Code consistency](http://squizlabs.github.io/PHP_CodeSniffer/analysis/squizlabs/PHP_CodeSniffer/grade.svg)](http://squizlabs.github.io/PHP_CodeSniffer/analysis/squizlabs/PHP_CodeSniffer)

Requirements
------------

PHP\_CodeSniffer requires PHP version 5.1.2 or greater, although individual sniffs may have additional requirements such as external applications and scripts. See the [Configuration Options manual page](http://pear.php.net/manual/en/package.php.php-codesniffer.config-options.php) for a list of these requirements.

The SVN pre-commit hook requires PHP version 5.2.4 or greater due to its use of the vertical whitespace character.

Installation
------------

The easiest way to install PHP\_CodeSniffer is to use the PEAR installer. This will make the `phpcs` command immediately available for use. To install PHP\_CodeSniffer using the PEAR installer, first ensure you have [installed PEAR](http://pear.php.net/manual/en/installation.getting.php) and then run the following command:

    pear install PHP_CodeSniffer

If you prefer using [Composer](http://getcomposer.org/) you can easily install PHP_CodeSniffer system-wide with the following command:

    composer global require "squizlabs/php_codesniffer=*"

Make sure you have `~/.composer/vendor/bin/` in your PATH.

Or alternatively, include a dependency for `squizlabs/php_codesniffer` in your `composer.json` file. For example:

    {
        "require-dev": {
            "squizlabs/php_codesniffer": "1.*"
        }
    }

You will then be able to run PHP_CodeSniffer from the vendor bin directory:

    ./vendor/bin/phpcs -h

You can also download the PHP\_CodeSniffer source and run the `phpcs` command directly from the GIT checkout:

    git clone git://github.com/squizlabs/PHP_CodeSniffer.git
    cd PHP_CodeSniffer
    php scripts/phpcs -h

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