Configuration Options
=====================

Table of contents
-----------------

-  `Setting the default coding
   standard <#setting-the-default-coding-standard>`__
-  `Setting the default report
   format <#setting-the-default-report-format>`__
-  `Hiding warnings by default <#hiding-warnings-by-default>`__
-  `Showing progress by default <#showing-progress-by-default>`__
-  `Using colors in output by
   default <#using-colors-in-output-by-default>`__
-  `Changing the default severity
   levels <#changing-the-default-severity-levels>`__
-  `Setting the default report
   width <#setting-the-default-report-width>`__
-  `Setting the default encoding <#setting-the-default-encoding>`__
-  `Setting the default tab width <#setting-the-default-tab-width>`__
-  `Setting the installed standard
   paths <#setting-the-installed-standard-paths>`__
-  `Setting the PHP version <#setting-the-php-version>`__
-  `Ignoring errors when generating the exit
   code <#ignoring-errors-when-generating-the-exit-code>`__
-  `Ignoring warnings when generating the exit
   code <#ignoring-warnings-when-generating-the-exit-code>`__
-  Setting tool paths

   -  `CSSLint <#setting-the-path-to-csslint>`__
   -  `Google Closure
      Linter <#setting-the-path-to-the-google-closure-linter>`__
   -  `PHP <#setting-the-path-to-php>`__
   -  `JSHint <#setting-the-path-to-jshint>`__
   -  `JSLint <#setting-the-path-to-jslint>`__
   -  `JavaScript Lint <#setting-the-path-to-javascript-lint>`__
   -  `Zend Code
      Analyzer <#setting-the-path-to-the-zend-code-analyzer>`__

--------------

Setting the default coding standard
-----------------------------------

By default, PHP\_CodeSniffer will use the PEAR coding standard if no
standard is supplied on the command line. You can change the default
standard by setting the default\_standard configuration option.

::

    $ phpcs --config-set default_standard Squiz

Setting the default report format
---------------------------------

By default, PHP\_CodeSniffer will use the full report format if no
format is supplied on the command line. You can change the default
report format by setting the report\_format configuration option.

::

    $ phpcs --config-set report_format summary

Hiding warnings by default
--------------------------

By default, PHP\_CodeSniffer will show both errors and warnings for your
code. You can hide warnings for a single script run by using the ``-n``
command line argument, but you can also enable this by default if you
prefer. To hide warnings by default, set the ``show_warnings``
configuration option to ``0``.

::

    $ phpcs --config-set show_warnings 0

    Note: When warnings are hidden by default, you can use the ``-w``
    command line argument to show them for a single script run.

Showing progress by default
---------------------------

By default, PHP\_CodeSniffer will run quietly and only print the report
of errors and warnings at the end. If you want to know what is happening
you can turn on progress output, but you can also enable this by default
if you prefer. To show progress by default, set the ``show_progress``
configuration option to ``1``.

::

    $ phpcs --config-set show_progress 1

Using colors in output by default
---------------------------------

By default, PHP\_CodeSniffer will not use colors in progress or report
screen output. To use colors in output by default, set the ``colors``
configuration option to ``1``.

::

    $ phpcs --config-set colors 1

    Note: When colors are being used by default, you can use the
    ``--no-colors`` command line argument to disable them for a single
    script run.

Changing the default severity levels
------------------------------------

By default, PHP\_CodeSniffer will show all errors and warnings with a
severity level of 5 or greater. You can change these settings for a
single script run by using the ``--severity``, ``--error-severity`` and
``--warning-severity`` command line arguments, but you can also change
the default settings if you prefer.

To change the default severity level to show all errors and warnings:

::

    $ phpcs --config-set severity 1

To change the default severity levels to show all errors but only some
warnings

::

    $ phpcs --config-set error_severity 1
    $ phpcs --config-set warning_severity 8

    Note: Setting the severity of warnings to 0 is the same as using the
    ``-n`` command line argument. If you set the severity of errors to
    ``0`` PHP\_CodeSniffer will not show any errors, which may be useful
    if you just want to show warnings.

Setting the default report width
--------------------------------

By default, PHP\_CodeSniffer will print all screen-based reports 80
characters wide. File paths will be truncated if they don't fit within
this limit and error messages will be wrapped across multiple lines. You
can increase the report width to show longer file paths and limit the
wrapping of error messages using the ``--report-width`` command line
argument, but you can also change the default report width by setting
the ``report_width`` configuration option.

::

    $ phpcs --config-set report_width 120

    Note: If you want reports to fill the entire terminal width (in
    supported terminals), set the ``report_width`` config configuration
    option to ``auto``.

    ``$phpcs --config-set report_width auto``

Setting the default encoding
----------------------------

By default, PHP\_CodeSniffer will treat all source files as if they use
ISO-8859-1 encoding. This can cause double-encoding problems when
generating UTF-8 encoded XML reports. To help PHP\_CodeSniffer encode
reports correctly, you can specify the encoding of your source files
using the ``--encoding`` command line argument, but you can also change
the default encoding by setting the ``encoding`` configuration option.

::

    $ phpcs --config-set encoding utf-8

Setting the default tab width
-----------------------------

By default, PHP\_CodeSniffer will not convert tabs to spaces in checked
files. Specifying a tab width will make PHP\_CodeSniffer replace tabs
with spaces. You can force PHP\_CodeSniffer to replace tabs with spaces
by default by setting the ``tab_width`` configuration option.

::

    $ phpcs --config-set tab_width 4

When the tab width is set by default, the replacement of tabs with
spaces can be disabled for a single script run by setting the tab width
to zero.

::

    $ phpcs --tab-width=0 /path/to/code

Setting the installed standard paths
------------------------------------

By default, PHP\_CodeSniffer will look inside its own
``CodeSniffer/Standards`` directory to find installed coding standards.
An installed standard appears when you use the ``-i`` command line
argument and can be referenced using a name instead of a path when using
the ``--standard`` command line argument. You can add install paths by
setting the ``installed_paths`` configuration option.

::

    $ phpcs --config-set installed_paths /path/to/one,/path/to/two

Setting the PHP version
-----------------------

Some sniffs change their behaviour based on the version of PHP being
used to run PHPCS. For example, a sniff that checks for namespaces may
choose to ignore this check if the version of PHP does not include
namespace support. Sometimes a code base that supports older PHP
versions is checked using a newer PHP version. In this case, sniffs see
the new PHP version and report errors that may not be correct. To let
the sniffs know what version of PHP you are targeting, the
``php_version`` configuration option can be used.

::

    $ phpcs --config-set php_version 50403

    Note: The format of the ``php_version`` value is the same as the
    PHP\_VERSION\_ID constant. e.g., 50403 for version 5.4.3.

Ignoring errors when generating the exit code
---------------------------------------------

By default, PHP\_CodeSniffer will exit with a non-zero code if any
errors or warnings are found. If you want to display errors to the user,
but still return with a zero exit code if no warnings are found, you can
set the ``ignore_errors_on_exit`` configuration option. This option is
typically used by automated build tools so that a list of errors can be
generated without failing the build.

::

    $ phpcs --config-set ignore_errors_on_exit 1

    If you want to generate a zero exit code in all cases, additionally
    set the ``ignore_warnings_on_exit`` config configuration option.

::

    $ phpcs --config-set ignore_errors_on_exit 1
    $ phpcs --config-set ignore_warnings_on_exit 1

Ignoring warnings when generating the exit code
-----------------------------------------------

By default, PHP\_CodeSniffer will exit with a non-zero code if any
errors or warnings are found. If you want to display warnings to the
user, but still return with a zero exit code if no errors are found, you
can set the ``ignore_warnings_on_exit`` configuration option. This
option is typically used by automated build tools so that a list of
warnings can be generated without failing the build.

::

    $ phpcs --config-set ignore_warnings_on_exit 1

Generic Coding Standard Configuration Options
---------------------------------------------

Setting the path to CSSLint
~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Generic coding standard `includes a
sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Generic/Sniffs/Debug/CSSLintSniff.php>`__
that will check each CSS file using `CSS Lint <http://csslint.net/>`__.
Use the ``csslint_path`` configuration option to tell the CSSLint sniff
where to find the tool.

::

    $ phpcs --config-set csslint_path /path/to/csslint

Setting the path to the Google Closure Linter
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Generic coding standard `includes a
sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Generic/Sniffs/Debug/ClosureLinterSniff.php>`__
that will check each file using the `Google Closure
Linter <https://github.com/google/closure-linter>`__, an open source
JavaScript style checker from Google. Use the ``gjslint_path``
configuration option to tell the Google Closure Linter sniff where to
find the tool.

::

    $ phpcs --config-set gjslint_path /path/to/gjslint

Setting the path to PHP
~~~~~~~~~~~~~~~~~~~~~~~

The Generic coding standard `includes a
sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Generic/Sniffs/PHP/SyntaxSniff.php>`__
that will check the syntax of each PHP file using `the built-in PHP
linter <http://php.net/manual/en/features.commandline.options.php>`__.
Use the ``php_path`` configuration option to tell the Syntax sniff where
to find the PHP binary.

::

    $ phpcs --config-set php_path /path/to/php

Setting the path to JSHint
~~~~~~~~~~~~~~~~~~~~~~~~~~

The Generic coding standard `includes a
sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Generic/Sniffs/Debug/JSHintSniff.php>`__
that will check each JavaScript file using
`JSHint <http://www.jshint.com/>`__, a tool to detect errors and
potential problems in JavaScript code. Use the ``jshint_path``
configuration option to tell the JSHint sniff where to find the tool.

::

    $ phpcs --config-set jshint_path /path/to/jshint.js

As JSHint is just JavaScript code, you also need to install
`Rhino <http://www.mozilla.org/rhino/>`__ to be able to execute it. Use
the ``rhino_path`` configuration option to tell the JSHint sniff where
to find the tool.

::

    $ phpcs --config-set rhino_path /path/to/rhino

Squiz Coding Standard Configuration Options
-------------------------------------------

Setting the path to JSLint
~~~~~~~~~~~~~~~~~~~~~~~~~~

The Squiz coding standard `includes a
sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/Debug/JSLintSniff.php>`__
that will check each JavaScript file using
`JSLint <http://www.jslint.com/>`__, a JavaScript program that looks for
problems in JavaScript programs. Use the ``jslint_path`` configuration
option to tell the JSLint sniff where to find the tool.

::

    $ phpcs --config-set jslint_path /path/to/jslint.js

As JSLint is just JavaScript code, you also need to install
`Rhino <https://developer.mozilla.org/en-US/docs/Rhino>`__ to be able to
execute it. Use the ``rhino_path`` configuration option to tell the
JSLint sniff where to find the tool.

::

    $ phpcs --config-set rhino_path /path/to/rhino

Setting the path to JavaScript Lint
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Squiz coding standard `includes a
sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/Debug/JavaScriptLintSniff.php>`__
that will check each JavaScript file using `JavaScript
Lint <http://www.javascriptlint.com/>`__, a tool that checks all your
JavaScript source code for common mistakes without actually running the
script or opening the web page. Use the ``jsl_path`` configuration
option to tell the JavaScript Lint sniff where to find the tool.

::

    $ phpcs --config-set jsl_path /path/to/jsl

Zend Coding Standard Configuration Options
------------------------------------------

Setting the path to the Zend Code Analyzer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Zend coding standard `includes a
sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Zend/Sniffs/Debug/CodeAnalyzerSniff.php>`__
that will check each file using the Zend Code Analyzer, a tool that
comes with Zend Studio. Use the ``zend_ca_path`` configuration option to
tell the Zend Code Analyzer sniff where to find the tool.

::

    $ phpcs --config-set zend_ca_path /path/to/ZendCodeAnalyzer
