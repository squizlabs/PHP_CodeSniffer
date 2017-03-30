Advanced Usage
==============

Table of contents
-----------------

-  `Specifying Valid File
   Extensions <#specifying-valid-file-extensions>`__
-  `Ignoring Files and Folders <#ignoring-files-and-folders>`__
-  `Ignoring Parts of a File <#ignoring-parts-of-a-file>`__
-  `Limiting Results to Specific
   Sniffs <#limiting-results-to-specific-sniffs>`__
-  `Filtering Errors and Warnings Based on
   Severity <#filtering-errors-and-warnings-based-on-severity>`__
-  `Replacing Tabs with Spaces <#replacing-tabs-with-spaces>`__
-  `Specifying an Encoding <#specifying-an-encoding>`__
-  `Using a Bootstrap File <#using-a-bootstrap-file>`__
-  `Using a Default Configuration
   File <#using-a-default-configuration-file>`__
-  `Specifying php.ini Settings <#specifying-phpini-settings>`__
-  `Setting Configuration Options <#setting-configuration-options>`__
-  `Deleting Configuration Options <#deleting-configuration-options>`__
-  `Viewing Configuration Options <#viewing-configuration-options>`__
-  `Printing Verbose Tokeniser
   Output <#printing-verbose-tokeniser-output>`__
-  `Printing Verbose Token Processing
   Output <#printing-verbose-token-processing-output>`__
-  `Quieting Output <#quieting-output>`__

--------------

Specifying Valid File Extensions
--------------------------------

By default, PHP\_CodeSniffer will check any file it finds with a
``.inc``, ``.php``, ``.js`` or ``.css`` extension, although not all
standards will actually check all these file types. Sometimes, this
means that PHP\_CodeSniffer is not checking enough of your files.
Sometimes, the opposite is true. PHP\_CodeSniffer allows you to specify
a list of valid file extensions using the ``--extensions`` command line
argument. Extensions are separated by commas.

To only check .php files:

::

    $ phpcs --extensions=php /path/to/code

To check .php, .inc and .lib files:

::

    $ phpcs --extensions=php,inc,lib /path/to/code

If you have asked PHP\_CodeSniffer to check a specific file rather than
an entire directory, the extension of the specified file will be
ignored. The file will be checked even if it has an invalid extension or
no extension at all. In the following example, the main.inc file will be
checked by PHP\_CodeSniffer even though the ``--extensions`` command
line argument specifies that only .php files should be checked.

::

    $ phpcs --extensions=php /path/to/code/main.inc

The ignoring of file extensions for specific files is a feature of
PHP\_CodeSniffer and is the only way to check files without an
extension. If you check an entire directory of files, all files without
extensions will be ignored, so you must check each of these file
separately.

Ignoring Files and Folders
--------------------------

Sometimes you want PHP\_CodeSniffer to run over a very large number of
files, but you want some files and folders to be skipped. The
``--ignore`` command line argument can be used to tell PHP\_CodeSniffer
to skip files and folders that match one or more patterns.

In the following example, PHP\_CodeSniffer will skip all files inside
the package's tests and data directories. This is useful if you are
checking a PEAR package but don't want your test or data files to
conform to your coding standard.

::

    $ phpcs --ignore=*/tests/*,*/data/* /path/to/code

    The ignore patterns can also be complete regular expressions. If you
    do specify a regular expression, be aware that ``*`` is converted to
    ``.*`` for the convenience in simple patterns, like those used in
    the example above. So use ``*`` anywhere you would normally use
    ``.*``.

You can also tell PHP\_CodeSniffer to ignore a file using a special
comment inserted at the top of the file. This will stop the file being
checked even if it does not match the ignore pattern.

.. code:: php

    <?php
    // @codingStandardsIgnoreFile
    $xmlPackage = new XMLPackage;
    $xmlPackage['error_code'] = get_default_error_code_value();
    $xmlPackage->send();

Ignoring Parts of a File
------------------------

Some parts of your code may be unable to conform to your coding
standard. For example, you might have to break your standard to
integrate with an external library or web service. To stop
PHP\_CodeSniffer generating errors for this code, you can wrap it in
special comments. PHP\_CodeSniffer will then hide all errors and
warnings that are generated for these lines of code.

.. code:: php

    $xmlPackage = new XMLPackage;
    // @codingStandardsIgnoreStart
    $xmlPackage['error_code'] = get_default_error_code_value();
    $xmlPackage->send();
    // @codingStandardsIgnoreEnd

You can also ignore a single line using the
``@codingStandardsIgnoreLine`` comment. This comment will ignore the
line that the comment is on, and the following line. It is typically
used like this:

.. code:: php

    $xmlPackage = new XMLPackage;
    // @codingStandardsIgnoreLine
    $xmlPackage['error_code'] = get_default_error_code_value();
    $xmlPackage->send();

Or like this:

.. code:: php

    $xmlPackage = new XMLPackage;
    $xmlPackage['error_code'] = get_default_error_code_value(); // @codingStandardsIgnoreLine

    $xmlPackage->send();

Limiting Results to Specific Sniffs
-----------------------------------

By default, PHP\_CodeSniffer will check your code using all sniffs in
the specified standard. Sometimes you may want to find all occurrences
of a single error to eliminate it more quickly, or to exclude sniffs to
see if they are causing conflicts in your standard. PHP\_CodeSniffer
allows you to specify a list of sniffs to limit results to using the
``--sniffs`` command line argument, or a list of sniffs to exclude using
the ``--exclude`` command line argument. Sniff codes are separated by
commas.

    Note: All sniffs specified on the command line must be used in the
    coding standard you are using to check your files.

The following example will only run two sniffs over the code instead of
all sniffs in the PEAR standard:

::

    $ phpcs --standard=PEAR --sniffs=Generic.PHP.LowerCaseConstant,PEAR.WhiteSpace.ScopeIndent /path/to/code

The following example will run all sniffs in the PEAR standard except
for the two specificed:

::

    $ phpcs --standard=PEAR --exclude=Generic.PHP.LowerCaseConstant,PEAR.WhiteSpace.ScopeIndent /path/to/code

    Note: If you use both the ``--sniffs`` and ``--exclude`` command
    line arguments together, the ``--exclude`` list will be ignored.

Filtering Errors and Warnings Based on Severity
-----------------------------------------------

By default, PHP\_CodeSniffer assigns a severity of 5 to all errors and
warnings. Standards may change the severity of some messages so they are
hidden by default or even so that they are raised to indicate greater
importance. PHP\_CodeSniffer allows you to decide what the minimum
severity level must be to show a message in its report using the
``--severity`` command line argument.

To hide errors and warnings with a severity less than 3:

::

    $ phpcs --severity=3 /path/to/code

You can specify different values for errors and warnings using the
``--error-severity`` and ``--warning-severity`` command line arguments.

To show all errors, but only warnings with a severity of 8 or more:

::

    $ phpcs --error-severity=1 --warning-severity=8 /path/to/code

Setting the severity of warnings to ``0`` is the same as using the
``-n`` command line argument. If you set the severity of errors to ``0``
PHP\_CodeSniffer will not show any errors, which may be useful if you
just want to show warnings.

This feature is particularly useful during manual code reviews. During
normal development, or an automated build, you may want to only check
code formatting issues. But while during a code review, you may wish to
show less severe errors and warnings that may need manual peer review.

Replacing Tabs with Spaces
--------------------------

Most of the sniffs written for PHP\_CodeSniffer do not support the usage
of tabs for indentation and alignment. You can write your own sniffs
that check for tabs instead of spaces, but you can also get
PHP\_CodeSniffer to convert your tabs into spaces before a file is
checked. This allows you to use the existing space-based sniffs on your
tab-based files.

In the following example, PHP\_CodeSniffer will replace all tabs in the
files being checked with between 1 and 4 spaces, depending on the column
the tab indents to.

::

    $ phpcs --tab-width=4 /path/to/code

    Note: The `included
    sniff <https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php>`__
    that enforces space indentation will still generate errors even if
    you have replaced tabs with spaces using the ``--tab-width``
    setting. This sniff looks at the unmodified version of the code to
    check line indentation and so must be disabled in a [[custom
    ruleset.xml file\|Annotated ruleset.xml]] if you want to use tab
    indentation.

Specifying an Encoding
----------------------

Some PHP\_CodeSniffer reports output UTF-8 encoded XML, which can cause
problems if your files are already UTF-8 encoded. In this case, some
content from your files (generally comments) are used within error
messages and may be double-encoded. To help PHP\_CodeSniffer encode
reports correctly, you can specify the encoding of your source files
using the ``--encoding`` command line argument.

::

    $ phpcs --encoding=utf-8 /path/to/code

The default encoding used by PHP\_CodeSniffer is ISO-8859-1.

Using a Bootstrap File
----------------------

PHP\_CodeSniffer can optionally include one or more custom bootstrap
files before beginning the run. Bootstrap files are included after
command line arguments and rulesets have been parsed, and right before
files begin to process. These custom files may be used to perform such
taks as manipulating the internal settings of PHP\_CodeSniffer that are
not exposed through command line arguments. Multiple bootstrap files are
seperated by commas.

::

    $ phpcs --bootstrap=/path/to/boostrap.1.inc,/path/to/bootstrap.2.inc /path/to/code

Using a Default Configuration File
----------------------------------

If you run PHP\_CodeSniffer without specifying a coding standard,
PHP\_CodeSniffer will look in the current directory, and all parent
directories, for a file called either ``phpcs.xml`` or
``phpcs.xml.dist``. If found, configuration information will be read
from this file, including the files to check, the coding standard to
use, and any command line arguments to apply.

    Note: If both a phpcs.xml and a phpcs.xml.dist file are found,
    PHP\_CodeSniffer will use the phpcs.xml file.

The ``phpcs.xml`` file has exactly the same format as a normal
[[ruleset.xml file\|Annotated ruleset.xml]], so all the same options are
available in it. The ``phpcs.xml`` file essentially acts as a default
coding standard and configuration file for a code base, and is typically
used to allow the ``phpcs`` command to be run on a repository without
specifying any arguments.

    An example ``phpcs.xml`` file can be found in the PHP\_CodeSniffer
    repository:
    `phpcs.xml.dist <https://raw.githubusercontent.com/squizlabs/PHP_CodeSniffer/master/phpcs.xml.dist>`__

Specifying php.ini Settings
---------------------------

PHP\_CodeSniffer allows you to set temporary php.ini settings during a
run using the ``-d`` command line argument. The name of the php.ini
setting must be specified on the command line, but the value is
optional. If no value is set, the php.ini setting will be given a value
of TRUE.

::

    $ phpcs -d memory_limit=32M /path/to/code

You can also specific multiple values:

::

    $ phpcs -d memory_limit=32M -d include_path=.:/php/includes /path/to/code

Setting Configuration Options
-----------------------------

PHP\_CodeSniffer has some configuration options that can be set.
Individual coding standards may also require configuration options to be
set before functionality can be used. [[View a full list of
configuration options\|Configuration Options]].

To set a configuration option, use the ``--config-set`` command line
argument.

::

    $ phpcs --config-set <option> <value>

Configuration options are written to a global configuration file. If you
want to set them for a single run only, use the ``--runtime-set``
command line argument.

::

    $ phpcs --runtime-set <option> <value> /path/to/code

Deleting Configuration Options
------------------------------

PHP\_CodeSniffer allows you to delete any configuration option,
reverting it to its default value. [[View a full list of configuration
options\|Configuration Options]].

To delete a configuration option, use the ``--config-delete`` command
line argument.

::

    $ phpcs --config-delete <option>

Viewing Configuration Options
-----------------------------

To view the currently set configuration options, use the
``--config-show`` command line argument.

::

    $ phpcs --config-show
    Array
    (
        [default_standard] => PEAR
        [zend_ca_path] => /path/to/ZendCodeAnalyzer
    )

Printing Verbose Tokeniser Output
---------------------------------

This feature is provided for debugging purposes only. Using this feature
will dramatically increase screen output and script running time.

PHP\_CodeSniffer contains multiple verbosity levels. Level 2 (indicated
by the command line argument ``-vv``) will print all verbosity
information for level 1 (file specific token and line counts with
running times) as well as verbose tokeniser output.

The output of the PHP\_CodeSniffer tokeniser shows the step-by-step
creation of the scope map and the level map.

The Scope Map
~~~~~~~~~~~~~

The scope map is best explained with an example. For the following file:

::

    <?php
    if ($condition) {
        echo 'Condition was true';
    }
    ?>

The scope map output is:

::

    *** START SCOPE MAP ***
    Start scope map at 1: T_IF => if
    Process token 2 []: T_WHITESPACE =>  
    Process token 3 []: T_OPEN_PARENTHESIS => (
    * skipping parenthesis *
    Process token 6 []: T_WHITESPACE =>  
    Process token 7 []: T_OPEN_CURLY_BRACKET => {
    => Found scope opener for 1 (T_IF)
    Process token 8 [opener:7;]: T_WHITESPACE => \n
    Process token 9 [opener:7;]: T_WHITESPACE =>     
    Process token 10 [opener:7;]: T_ECHO => echo
    Process token 11 [opener:7;]: T_WHITESPACE =>  
    Process token 12 [opener:7;]: T_CONSTANT_ENCAPSED_STRING => 'Condition was true'
    Process token 13 [opener:7;]: T_SEMICOLON => ;
    Process token 14 [opener:7;]: T_WHITESPACE => \n
    Process token 15 [opener:7;]: T_CLOSE_CURLY_BRACKET => }
    => Found scope closer for 1 (T_IF)
    *** END SCOPE MAP ***

The scope map output above shows the following pieces of information
about the file:

-  A scope token ``if`` was found at token 1 (note that token 0 is the
   open PHP tag).
-  The opener for the if statement, the open curly brace, was found at
   token 7.
-  The closer for the if statement, the close curly brace, was found at
   token 15.
-  Tokens 8 - 15 are all included in the scope set by the scope opener
   at token 7, the open curly brace. This indicates that these tokens
   are all within the if statement.

The scope map output is most useful when debugging PHP\_CodeSniffer's
scope map, which is critically important to the successful checking of a
file, but is also useful for checking the type of a particular token.
For example, if you are unsure of the token type for an opening curly
brace, the scope map output shows you that the type is
T\_OPEN\_CURLY\_BRACKET and not, for example, T\_OPEN\_CURLY\_BRACE.

The Level Map
~~~~~~~~~~~~~

The level map is best explained with an example. For the following file:

::

    <?php
    if ($condition) {
        echo 'Condition was true';
    }
    ?>

The level map output is:

::

    *** START LEVEL MAP ***
    Process token 0 on line 1 [lvl:0;]: T_OPEN_TAG => <?php\n
    Process token 1 on line 2 [lvl:0;]: T_IF => if
    Process token 2 on line 2 [lvl:0;]: T_WHITESPACE =>  
    Process token 3 on line 2 [lvl:0;]: T_OPEN_PARENTHESIS => (
    Process token 4 on line 2 [lvl:0;]: T_VARIABLE => $condition
    Process token 5 on line 2 [lvl:0;]: T_CLOSE_PARENTHESIS => )
    Process token 6 on line 2 [lvl:0;]: T_WHITESPACE =>  
    Process token 7 on line 2 [lvl:0;]: T_OPEN_CURLY_BRACKET => {
    => Found scope opener for 1 (T_IF)
        * level increased *
        * token 1 (T_IF) added to conditions array *
        Process token 8 on line 2 [lvl:1;conds;T_IF;]: T_WHITESPACE => \n
        Process token 9 on line 3 [lvl:1;conds;T_IF;]: T_WHITESPACE =>     
        Process token 10 on line 3 [lvl:1;conds;T_IF;]: T_ECHO => echo
        Process token 11 on line 3 [lvl:1;conds;T_IF;]: T_WHITESPACE =>  
        Process token 12 on line 3 [lvl:1;conds;T_IF;]: T_CONSTANT_ENCAPSED_STRING => 'Condition was true'
        Process token 13 on line 3 [lvl:1;conds;T_IF;]: T_SEMICOLON => ;
        Process token 14 on line 3 [lvl:1;conds;T_IF;]: T_WHITESPACE => \n
        Process token 15 on line 4 [lvl:1;conds;T_IF;]: T_CLOSE_CURLY_BRACKET => }
        => Found scope closer for 7 (T_OPEN_CURLY_BRACKET)
        * token T_IF removed from conditions array *
        * level decreased *
    Process token 16 on line 4 [lvl:0;]: T_WHITESPACE => \n
    Process token 17 on line 5 [lvl:0;]: T_CLOSE_TAG => ?>\n
    *** END LEVEL MAP ***

The level map output above shows the following pieces of information
about the file:

-  A scope opener, an open curly brace, was found at token 7 and opened
   the scope for an if statement, defined at token 1.
-  Tokens 8 - 15 are all included in the scope set by the scope opener
   at token 7, the open curly brace. All these tokens are at level 1,
   indicating that they are enclosed in 1 scope condition, and all these
   tokens are enclosed in a single condition; an if statement.

The level map is most commonly used to determine indentation rules
(e.g., a token 4 levels deep requires 16 spaces of indentation) or to
determine if a particular token is within a particular scope (eg. a
function keyword is within a class scope, making it a method).

Printing Verbose Token Processing Output
----------------------------------------

This feature is provided for debugging purposes only. Using this feature
will dramatically increase screen output and script running time.

PHP\_CodeSniffer contains multiple verbosity levels. Level 3 (indicated
by the command line argument ``-vvv``) will print all verbosity
information for level 1 (file specific token and line counts with
running times), level 2 (tokeniser output) as well as token processing
output with sniff running times.

The token processing output is best explained with an example. For the
following file:

::

    <?php
    if ($condition) {
        echo 'Condition was true';
    }
    ?>

The token processing output is:

::

    *** START TOKEN PROCESSING ***
    Process token 0: T_OPEN_TAG => <?php\n
        Processing PEAR_Sniffs_Commenting_FileCommentSniff... DONE in 0 seconds
        Processing Generic_Sniffs_PHP_DisallowShortOpenTagSniff... DONE in 0 seconds
        Processing Generic_Sniffs_Files_LineLengthSniff... DONE in 0.0001 seconds
        Processing Generic_Sniffs_Files_LineEndingsSniff... DONE in 0 seconds
    Process token 1: T_IF => if
        Processing PEAR_Sniffs_ControlStructures_ControlSignatureSniff... DONE in 0.0001 seconds
        Processing PEAR_Sniffs_ControlStructures_MultiLineConditionSniff... DONE in 0 seconds
        Processing PEAR_Sniffs_WhiteSpace_ScopeClosingBraceSniff... DONE in 0 seconds
        Processing PEAR_Sniffs_WhiteSpace_ScopeIndentSniff... DONE in 0 seconds
        Processing Generic_Sniffs_ControlStructures_InlineControlStructureSniff... DONE in 0 seconds
    Process token 2: T_WHITESPACE =>  
        Processing Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff... DONE in 0 seconds
    Process token 3: T_OPEN_PARENTHESIS => (
    Process token 4: T_VARIABLE => $condition
        Processing PEAR_Sniffs_NamingConventions_ValidVariableNameSniff... DONE in 0 seconds
    Process token 5: T_CLOSE_PARENTHESIS => )
    Process token 6: T_WHITESPACE =>  
        Processing Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff... DONE in 0 seconds
    Process token 7: T_OPEN_CURLY_BRACKET => {
    Process token 8: T_WHITESPACE => \n
        Processing Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff... DONE in 0 seconds
    Process token 9: T_WHITESPACE =>     
        Processing Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff... DONE in 0 seconds
    Process token 10: T_ECHO => echo
    Process token 11: T_WHITESPACE =>  
        Processing Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff... DONE in 0 seconds
    Process token 12: T_CONSTANT_ENCAPSED_STRING => 'Condition was true'
    Process token 13: T_SEMICOLON => ;
    Process token 14: T_WHITESPACE => \n
        Processing Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff... DONE in 0 seconds
    Process token 15: T_CLOSE_CURLY_BRACKET => }
    Process token 16: T_WHITESPACE => \n
        Processing Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff... DONE in 0 seconds
    Process token 17: T_CLOSE_TAG => ?>\n
    *** END TOKEN PROCESSING ***

Every token processed is shown, along with its ID, type and contents.
For each token, all sniffs that were executed on the token are
displayed, along with the running time.

For example, the output above shows us that token 1, an if keyword, had
5 sniffs executed on it; the ControlSignature sniff, the
MultiLineCondition sniff, the ScopeClosingBrace sniff, the ScopeIndent
sniff and the InlineControlStructure sniff. Each was executed fairly
quickly, but the slowest was the ControlSignature sniff, taking 0.0001
seconds to process that token.

The other interesting piece of information we get from the output above
is that some tokens didn't have any sniffs executed on them. This is
normal behaviour for PHP\_CodeSniffer as most sniffs listen for specific
or rarely used tokens and then execute on it and a number of tokens
following it.

For example, the ScopeIndentSniff executes on the if statement's token
only, but actually checks the indentation of every line within the if
statement. The sniff uses the scope map to find all tokens within the if
statement.

Quieting Output
---------------

If a coding standard or configuration file includes settings to print
progress or verbose output while running PHP\_CodeSniffer, it can make
it difficult to use the standard with automated checking tools and build
scripts as these typically only expect an error report. If you have this
problem, or just want less output, you can quiten the output of
PHP\_CodeSniffer by using the ``-q`` command line argument. When using
this quiet mode, PHP\_CodeSniffer will only print report output, and
only if errors or warnings are found. No progress or verbose output will
be printed.
