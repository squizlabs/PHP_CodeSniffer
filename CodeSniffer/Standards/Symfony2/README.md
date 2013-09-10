Symfony2 PHP CodeSniffer Coding Standard
========================================

A code standard to check against the [Symfony coding standards](http://symfony.com/doc/current/contributing/code/standards.html)

Installation
------------

1. Install phpcs:

        pear install PHP_CodeSniffer

2. Find your PEAR directory:

        pear config-show | grep php_dir

3. Copy, symlink or check out this repo to a folder called Symfony2 inside the
   phpcs `Standards` directory:

        cd /path/to/pear/PHP/CodeSniffer/Standards
        git clone git://github.com/opensky/Symfony2-coding-standard.git Symfony2

4. Set Symfony2 as your default coding standard:

        phpcs --config-set default_standard Symfony2

5. ...

6. Profit!

        cd /path/to/my/project
        phpcs
        phpcs path/to/my/file.php


Contributing
------------

If you do contribute code to these sniffs, please make sure it conforms to the PEAR
coding standard and that the Symfony2-coding-standard unit tests still pass.

To check the coding standard, run from the Symfony2-coding-standard source root:

    $ phpcs --ignore=*/tests/* --standard=PEAR . -n

The unit-tests are run from within the PHP_CodeSniffer directory:

    $ phpunit --filter Symfony2_* tests/AllTests.php
