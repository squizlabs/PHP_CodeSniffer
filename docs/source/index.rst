PHP\_CodeSniffer is a set of two PHP scripts; the main phpcs script that
tokenizes PHP, JavaScript and CSS files to detect violations of a
defined coding standard, and a second phpcbf script to automatically
correct coding standard violations. PHP\_CodeSniffer is an essential
development tool that ensures your code remains clean and consistent.

A coding standard in PHP\_CodeSniffer is a collection of sniff files.
Each sniff file checks one part of the coding standard only. Multiple
coding standards can be used within PHP\_CodeSniffer so that the one
installation can be used across multiple projects. The default coding
standard used by PHP\_CodeSniffer is the PEAR coding standard.

Example
-------

To check a file against the PEAR coding standard, simply specify the
file's location.

::

    $ phpcs /path/to/code/myfile.php

    FILE: /path/to/code/myfile.php
    --------------------------------------------------------------------------------
    FOUND 5 ERROR(S) AFFECTING 2 LINE(S)
    --------------------------------------------------------------------------------
      2 | ERROR | Missing file doc comment
     20 | ERROR | PHP keywords must be lowercase; expected "false" but found "FALSE"
     47 | ERROR | Line not indented correctly; expected 4 spaces but found 1
     51 | ERROR | Missing function doc comment
     88 | ERROR | Line not indented correctly; expected 9 spaces but found 6
    --------------------------------------------------------------------------------

Or, if you wish to check an entire directory, you can specify the
directory location instead of a file.

::

    $ phpcs /path/to/code

    FILE: /path/to/code/myfile.php
    --------------------------------------------------------------------------------
    FOUND 5 ERROR(S) AFFECTING 5 LINE(S)
    --------------------------------------------------------------------------------
      2 | ERROR | Missing file doc comment
     20 | ERROR | PHP keywords must be lowercase; expected "false" but found "FALSE"
     47 | ERROR | Line not indented correctly; expected 4 spaces but found 1
     51 | ERROR | Missing function doc comment
     88 | ERROR | Line not indented correctly; expected 9 spaces but found 6
    --------------------------------------------------------------------------------

    FILE: /path/to/code/yourfile.php
    --------------------------------------------------------------------------------
    FOUND 1 ERROR(S) AND 1 WARNING(S) AFFECTING 1 LINE(S)
    --------------------------------------------------------------------------------
     21 | ERROR   | PHP keywords must be lowercase; expected "false" but found
        |         | "FALSE"
     21 | WARNING | Equals sign not aligned with surrounding assignments
    --------------------------------------------------------------------------------

.. toctree::
   :glob:
   :hidden:

   Requirements
   Usage
   Advanced-Usage
   Reporting
   Configuration-Options
   Fixing-Errors-Automatically
   FAQ

   Annotated-ruleset.xml
   Customisable-Sniff-Properties
   Using-the-SVN-pre-commit-Hook

   Coding-Standard-Tutorial
   Version-3.0-Upgrade-Guide
