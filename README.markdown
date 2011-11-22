About
-----

PHP\_CodeSniffer is a PHP5 script that tokenises PHP, JavaScript and CSS files to detect violations of a defined coding standard. It is an essential development tool that ensures your code remains clean and consistent. It can also help prevent some common semantic errors made by developers.

[![Build Status](https://secure.travis-ci.org/squizlabs/PHP_CodeSniffer.png)](http://travis-ci.org/squizlabs/PHP_CodeSniffer)

Requirements
------------

PHP\_CodeSniffer requires PHP version 5.1.2 or greater, although individual sniffs may have additional requirements such as external applications and scripts. See the [Configuration Options manual page](http://pear.php.net/manual/en/package.php.php-codesniffer.config-options.php) for a list of these requirements.

The SVN pre-commit hook requires PHP version 5.2.4 or greater due to its use of the vertical whitespace character.

Installation
------------

The easiest way to install PHP\_CodeSniffer is to use the PEAR installer. This will make the `phpcs` command immediately available for use. To install PHP\_CodeSniffer using the PEAR installer, first ensure you have [installed PEAR](http://pear.php.net/manual/en/installation.getting.php) and then run the following command:

    pear install PHP_CodeSniffer

If you don't want to install PEAR, you can download the PHP\_CodeSniffer source and run the `phpcs` command directly from the GIT checkout:

    git clone git://github.com/squizlabs/PHP_CodeSniffer.git
    cd PHP_CodeSniffer
    php scripts/phpcs -h

Documentation
-------------

The documentation for PHP\_CodeSniffer is available in the [PEAR manual](http://pear.php.net/manual/en/package.php.php-codesniffer.php).

Information about upcoming features and releases is available on the [Squiz Labs blog](http://www.squizlabs.com/php-codesniffer).

Issues
------

Bug reports and feature requests can be submitted on the [PEAR bug tracker](http://pear.php.net/package/PHP_CodeSniffer/bugs).
