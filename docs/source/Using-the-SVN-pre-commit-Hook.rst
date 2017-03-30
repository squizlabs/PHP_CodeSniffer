Using the SVN pre commit Hook
=============================

    The SVN pre-commit hook will be removed and unavailable from version
    3.0.0 onwards.

    The SVN pre-commit hook has different requirements than the main
    PHP\_CodeSniffer package. See the [[Requirements]] page for more
    information.

A pre-commit hook is a feature available in the
`Subversion <http://subversion.tigris.org>`__ version control system
that allows code to be validated before it is committed to the
repository. The PHP\_CodeSniffer pre-commit hook allows you to check
code for coding standard errors and stop the commit process if errors
are found. This ensures developers are not able to commit code that
violates your coding standard. Instead, they are presented with the list
of errors they need to correct before committing.

::

    $ svn commit -m "Test" temp.php
    Sending        temp.php
    Transmitting file data .svn: Commit failed (details follow):
    svn: 'pre-commit' hook failed with error output:

    FILE: temp.php
    ---------------------------------------------------------------
    FOUND 1 ERROR(S) AND 0 WARNING(S) AFFECTING 1 LINE(S)
    ---------------------------------------------------------------
     2 | ERROR | Missing file doc comment
    --------------------------------------------------------------

Configuring the pre commit Hook
-------------------------------

Edit ``/path/to/PHP_CodeSniffer/scripts/phpcs-svn-pre-commit`` and
replace ``@php_bin@`` in the first line with the path to the PHP CLI.
For example, ``#!@php_bin@`` becomes ``#!/usr/bin/php``.

Now ensure the path to ``svnlook`` is correct by modifying the following
line, if required:

::

    define('PHP_CODESNIFFER_SVNLOOK', '/usr/bin/svnlook');

Now add the following line to your pre-commit file in the Subversion
hooks directory:

::

    /path/to/PHP_CodeSniffer/scripts/phpcs-svn-pre-commit "$REPOS" -t "$TXN" >&2 || exit 1

You can also use all the standard ``phpcs`` command line options to do
things like set the standard to use, the tab width and the error report
format:

::

    /path/to/PHP_CodeSniffer/scripts/phpcs-svn-pre-commit --standard=Squiz --tab-width=4 "$REPOS" -t "$TXN" >&2 || exit 1
