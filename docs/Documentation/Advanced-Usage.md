## Table of contents
  * [Specifying Valid File Extensions](#specifying-valid-file-extensions)
  * [Ignoring Files and Folders](#ignoring-files-and-folders)
  * [Ignoring Parts of a File](#ignoring-parts-of-a-file)
  * [Limiting Results to Specific Sniffs](#limiting-results-to-specific-sniffs)
  * [Filtering Errors and Warnings Based on Severity](#filtering-errors-and-warnings-based-on-severity)
  * [Replacing Tabs with Spaces](#replacing-tabs-with-spaces)
  * [Specifying an Encoding](#specifying-an-encoding)
  * [Using a Bootstrap File](#using-a-bootstrap-file)
  * [Using a Default Configuration File](#using-a-default-configuration-file)
  * [Specifying php.ini Settings](#specifying-phpini-settings)
  * [Setting Configuration Options](#setting-configuration-options)
  * [Deleting Configuration Options](#deleting-configuration-options)
  * [Viewing Configuration Options](#viewing-configuration-options)
  * [Printing Verbose Tokeniser Output](#printing-verbose-tokeniser-output)
  * [Printing Verbose Token Processing Output](#printing-verbose-token-processing-output)
  * [Quieting Output](#quieting-output)

***

## Specifying Valid File Extensions
By default, PHP_CodeSniffer will check any file it finds with a `.inc`, `.php`, `.js` or `.css` extension, although not all standards will actually check all these file types. Sometimes, this means that PHP_CodeSniffer is not checking enough of your files. Sometimes, the opposite is true. PHP_CodeSniffer allows you to specify a list of valid file extensions using the `--extensions` command line argument. Extensions are separated by commas.

To only check .php files:

    $ phpcs --extensions=php /path/to/code

To check .php, .inc and .lib files:

    $ phpcs --extensions=php,inc,lib /path/to/code

## Ignoring Files and Folders
Sometimes you want PHP_CodeSniffer to run over a very large number of files, but you want some files and folders to be skipped. The `--ignore` command line argument can be used to tell PHP_CodeSniffer to skip files and folders that match one or more patterns.

In the following example, PHP_CodeSniffer will skip all files inside the package's tests and data directories. This is useful if you are checking a PEAR package but don't want your test or data files to conform to your coding standard.

    $ phpcs --ignore=*/tests/*,*/data/* /path/to/code

> The ignore patterns are treated as regular expressions. If you do specify a regular expression, be aware that `*` is converted to `.*` for the convenience in simple patterns, like those used in the example above. So use `*` anywhere you would normally use `.*`. Also ensure you escape any `.` characters that you want treated as a literal dot, such as when checking file extensions. So if you are checking for `.inc` in your ignore pattern, use `\.inc` instead. 

You can also tell PHP_CodeSniffer to ignore a file using a special comment inserted at the top of the file. This will stop the file being checked even if it does not match the ignore pattern.

```php
<?php
// phpcs:ignoreFile
$xmlPackage = new XMLPackage;
$xmlPackage['error_code'] = get_default_error_code_value();
$xmlPackage->send();
```

> Note: Before PHP_CodeSniffer version 3.2.0, use `// @codingStandardsIgnoreFile` instead of `// phpcs:ignoreFile`. The `@codingStandards` syntax is deprecated and will be removed in PHP_CodeSniffer version 4.0.

> Note: The `phpcs:ignoreFile` comment syntax does not allow for a specific set of sniffs to be ignored for a file. Use the `phpcs:disable` comment syntax if you want to disable a specific set of sniffs for the entire file.

If required, you can add a note explaining why the file is being ignored by using the `--` separator.

```php
<?php
// phpcs:ignoreFile -- this is not a core file
$xmlPackage = new XMLPackage;
$xmlPackage['error_code'] = get_default_error_code_value();
$xmlPackage->send();
```

> Note: The comment syntax note feature is only available from PHP_CodeSniffer version 3.2.0 onwards.

## Ignoring Parts of a File
Some parts of your code may be unable to conform to your coding standard. For example, you might have to break your standard to integrate with an external library or web service. To stop PHP_CodeSniffer generating errors for this code, you can wrap it in special comments. PHP_CodeSniffer will then hide all errors and warnings that are generated for these lines of code.

```php
$xmlPackage = new XMLPackage;
// phpcs:disable
$xmlPackage['error_code'] = get_default_error_code_value();
$xmlPackage->send();
// phpcs:enable
```

> Note: Before PHP_CodeSniffer version 3.2.0, use `// @codingStandardsIgnoreStart` instead of `// phpcs:disable`, and use `// @codingStandardsIgnoreEnd` instead of `// phpcs:enable`. The `@codingStandards` syntax is deprecated and will be removed in PHP_CodeSniffer version 4.0.

If you don't want to disable all coding standard errors, you can selectively disable and re-enable specific error message codes, sniffs, categories of sniffs, or entire coding standards. The following example disables the specific `Generic.Commenting.Todo.Found` message and then re-enables all checks at the end.

```php
// phpcs:disable Generic.Commenting.Todo.Found
$xmlPackage = new XMLPackage;
$xmlPackage['error_code'] = get_default_error_code_value();
// TODO: Add an error message here.
$xmlPackage->send();
// phpcs:enable
```

You can disable multiple error message codes, sniff, categories, or standards by using a comma separated list. You can also selectively re-enable just the ones you want. The following example disables the entire PEAR coding standard, and all the Squiz array sniffs, before selectively re-enabling a specific sniff. It then re-enables all checking rules at the end.

```php
// phpcs:disable PEAR,Squiz.Arrays
$foo = [1,2,3];
bar($foo,true);
// phpcs:enable PEAR.Functions.FunctionCallSignature
bar($foo,false);
// phpcs:enable
```

> Note: All `phpcs:disable` and `phpcs:enable` comments only apply to the file they are contained within. After the file has finished processing all sniffs are re-enabled for future files.

> Note: Selective disabling and re-enabling of codes/sniffs/categories/standards is only available from PHP_CodeSniffer version 3.2.0 onwards.

You can also ignore a single line using the `phpcs:ignore` comment. This comment will ignore the line that the comment is on, and the following line. It is typically used like this:

```php
// phpcs:ignore
$foo = [1,2,3];
bar($foo, false);
```

Or like this:

```php
$foo = [1,2,3]; // phpcs:ignore
bar($foo, false);
```

> Note: Before PHP_CodeSniffer version 3.2.0, use `// @codingStandardsIgnoreLine` instead of `// phpcs:ignore`. The `@codingStandards` syntax is deprecated and will be removed in PHP_CodeSniffer version 4.0.

Again, you can selectively ignore one or more specific error message codes, sniffs, categories of sniffs, or entire standards.

```php
// phpcs:ignore Squiz.Arrays.ArrayDeclaration.SingleLineNotAllowed
$foo = [1,2,3];
bar($foo, false);
```

> Note: Selective ignoring of codes/sniffs/categories/standards is only available from PHP_CodeSniffer version 3.2.0 onwards.

If required, you can add a note explaining why sniffs are being disable and re-enabled by using the `--` separator.

```php
// phpcs:disable PEAR,Squiz.Arrays -- this isn't our code
$foo = [1,2,3];
bar($foo,true);
// phpcs:enable PEAR.Functions.FunctionCallSignature -- check function calls again
bar($foo,false);
// phpcs:enable -- this is out code again, so turn everything back on
```

> Note: The comment syntax note feature is only available from PHP_CodeSniffer version 3.2.0 onwards.

## Limiting Results to Specific Sniffs
By default, PHP_CodeSniffer will check your code using all sniffs in the specified standard. Sometimes you may want to find all occurrences of a single error to eliminate it more quickly, or to exclude sniffs to see if they are causing conflicts in your standard. PHP_CodeSniffer allows you to specify a list of sniffs to limit results to using the `--sniffs` command line argument, or a list of sniffs to exclude using the `--exclude` command line argument. Sniff codes are separated by commas.

> Note: All sniffs specified on the command line must be used in the coding standard you are using to check your files.

The following example will only run two sniffs over the code instead of all sniffs in the PEAR standard:

    $ phpcs --standard=PEAR --sniffs=Generic.PHP.LowerCaseConstant,PEAR.WhiteSpace.ScopeIndent /path/to/code

The following example will run all sniffs in the PEAR standard except for the two specified:

    $ phpcs --standard=PEAR --exclude=Generic.PHP.LowerCaseConstant,PEAR.WhiteSpace.ScopeIndent /path/to/code

> Note: If you use both the `--sniffs` and `--exclude` command line arguments together, the `--exclude` list will be ignored.

## Filtering Errors and Warnings Based on Severity
By default, PHP_CodeSniffer assigns a severity of 5 to all errors and warnings. Standards may change the severity of some messages so they are hidden by default or even so that they are raised to indicate greater importance. PHP_CodeSniffer allows you to decide what the minimum severity level must be to show a message in its report using the `--severity` command line argument.

To hide errors and warnings with a severity less than 3:

    $ phpcs --severity=3 /path/to/code

You can specify different values for errors and warnings using the `--error-severity` and `--warning-severity` command line arguments.

To show all errors, but only warnings with a severity of 8 or more:

    $ phpcs --error-severity=1 --warning-severity=8 /path/to/code

Setting the severity of warnings to `0` is the same as using the `-n` command line argument. If you set the severity of errors to `0` PHP_CodeSniffer will not show any errors, which may be useful if you just want to show warnings.

This feature is particularly useful during manual code reviews. During normal development, or an automated build, you may want to only check code formatting issues. But while during a code review, you may wish to show less severe errors and warnings that may need manual peer review.

## Replacing Tabs with Spaces
Most of the sniffs written for PHP_CodeSniffer do not support the usage of tabs for indentation and alignment. You can write your own sniffs that check for tabs instead of spaces, but you can also get PHP_CodeSniffer to convert your tabs into spaces before a file is checked. This allows you to use the existing space-based sniffs on your tab-based files.

In the following example, PHP_CodeSniffer will replace all tabs in the files being checked with between 1 and 4 spaces, depending on the column the tab indents to.

    $ phpcs --tab-width=4 /path/to/code

> Note: The [included sniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php) that enforces space indentation will still generate errors even if you have replaced tabs with spaces using the `--tab-width` setting. This sniff looks at the unmodified version of the code to check line indentation and so must be disabled in a [[custom ruleset.xml file|Annotated ruleset]] if you want to use tab indentation.

## Specifying an Encoding
By default, PHP_CodeSniffer will treat all source files as if they use UTF-8 encoding. If you need your source files to be processed using a specific encoding, you can specify the encoding using the `--encoding` command line argument.

    $ phpcs --encoding=windows-1251 /path/to/code

## Using a Bootstrap File
PHP_CodeSniffer can optionally include one or more custom bootstrap files before beginning the run. Bootstrap files are included after command line arguments and rulesets have been parsed, and right before files begin to process. These custom files may be used to perform such taks as manipulating the internal settings of PHP_CodeSniffer that are not exposed through command line arguments. Multiple bootstrap files are seperated by commas.

    $ phpcs --bootstrap=/path/to/boostrap.1.inc,/path/to/bootstrap.2.inc /path/to/code

## Using a Default Configuration File
If you run PHP_CodeSniffer without specifying a coding standard, PHP_CodeSniffer will look in the current directory, and all parent directories, for a file called either `.phpcs.xml`, `phpcs.xml`, `.phpcs.xml.dist`, or `phpcs.xml.dist`. If found, configuration information will be read from this file, including the files to check, the coding standard to use, and any command line arguments to apply.

> Note: If multiple default configuration files are found, PHP_CodeSniffer will select one using the following order: `.phpcs.xml`, `phpcs.xml`, `.phpcs.xml.dist`, `phpcs.xml.dist`

The `phpcs.xml` file has exactly the same format as a normal [[ruleset.xml file|Annotated ruleset]], so all the same options are available in it. The `phpcs.xml` file essentially acts as a default coding standard and configuration file for a code base, and is typically used to allow the `phpcs` command to be run on a repository without specifying any arguments.

> An example `phpcs.xml` file can be found in the PHP_CodeSniffer repository: [phpcs.xml.dist](https://raw.githubusercontent.com/squizlabs/PHP_CodeSniffer/master/phpcs.xml.dist)

## Specifying php.ini Settings
PHP_CodeSniffer allows you to set temporary php.ini settings during a run using the `-d` command line argument. The name of the php.ini setting must be specified on the command line, but the value is optional. If no value is set, the php.ini setting will be given a value of TRUE.

    $ phpcs -d memory_limit=32M /path/to/code

You can also specific multiple values:

    $ phpcs -d memory_limit=32M -d include_path=.:/php/includes /path/to/code

## Setting Configuration Options
PHP_CodeSniffer has some configuration options that can be set. Individual coding standards may also require configuration options to be set before functionality can be used. [[View a full list of configuration options|Configuration Options]].

To set a configuration option, use the `--config-set` command line argument.

    $ phpcs --config-set <option> <value>

Configuration options are written to a global configuration file. If you want to set them for a single run only, use the `--runtime-set` command line argument.

    $ phpcs --runtime-set <option> <value> /path/to/code

> Note: Not all configuration options can be set using the `--runtime-set` command line argument. Configuration options that provide defaults for command line arguments, such as the default standard or report type, can not be used with `--runtime-set`. To set these values for a single run only, use the dedicated CLI arguments that PHP_CodeSniffer provides. The [[Configuration Options|Configuration Options]] list provides an alternative CLI argument for each configuration option not supported by `--runtime-set`.

## Deleting Configuration Options
PHP_CodeSniffer allows you to delete any configuration option, reverting it to its default value. [[View a full list of configuration options|Configuration Options]].

To delete a configuration option, use the `--config-delete` command line argument.

    $ phpcs --config-delete <option>

   
## Viewing Configuration Options
To view the currently set configuration options, use the `--config-show` command line argument.

    $ phpcs --config-show
    Array
    (
        [default_standard] => PEAR
        [zend_ca_path] => /path/to/ZendCodeAnalyzer
    )

## Printing Verbose Tokeniser Output
This feature is provided for debugging purposes only. Using this feature will dramatically increase screen output and script running time.

PHP_CodeSniffer contains multiple verbosity levels. Level 2 (indicated by the command line argument `-vv`) will print all verbosity information for level 1 (file specific token and line counts with running times) as well as verbose tokeniser output.

The output of the PHP_CodeSniffer tokeniser shows the step-by-step creation of the scope map and the level map.

### The Scope Map
The scope map is best explained with an example. For the following file:

    <?php
    if ($condition) {
        echo 'Condition was true';
    }
    ?>

The scope map output is:

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
    
The scope map output above shows the following pieces of information about the file:
* A scope token `if` was found at token 1 (note that token 0 is the open PHP tag).
* The opener for the if statement, the open curly brace, was found at token 7.
* The closer for the if statement, the close curly brace, was found at token 15.
* Tokens 8 - 15 are all included in the scope set by the scope opener at token 7, the open curly brace. This indicates that these tokens are all within the if statement.

The scope map output is most useful when debugging PHP_CodeSniffer's scope map, which is critically important to the successful checking of a file, but is also useful for checking the type of a particular token. For example, if you are unsure of the token type for an opening curly brace, the scope map output shows you that the type is T_OPEN_CURLY_BRACKET and not, for example, T_OPEN_CURLY_BRACE.

### The Level Map 
The level map is best explained with an example. For the following file:

    <?php
    if ($condition) {
        echo 'Condition was true';
    }
    ?>

The level map output is:

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

    
The level map output above shows the following pieces of information about the file:
* A scope opener, an open curly brace, was found at token 7 and opened the scope for an if statement, defined at token 1.
* Tokens 8 - 15 are all included in the scope set by the scope opener at token 7, the open curly brace. All these tokens are at level 1, indicating that they are enclosed in 1 scope condition, and all these tokens are enclosed in a single condition; an if statement.

The level map is most commonly used to determine indentation rules (e.g., a token 4 levels deep requires 16 spaces of indentation) or to determine if a particular token is within a particular scope (e.g., a function keyword is within a class scope, making it a method).

## Printing Verbose Token Processing Output
This feature is provided for debugging purposes only. Using this feature will dramatically increase screen output and script running time.

PHP_CodeSniffer contains multiple verbosity levels. Level 3 (indicated by the command line argument `-vvv`) will print all verbosity information for level 1 (file specific token and line counts with running times), level 2 (tokeniser output) as well as token processing output with sniff running times.

The token processing output is best explained with an example. For the following file:

    <?php
    if ($condition) {
        echo 'Condition was true';
    }
    ?>

The token processing output is:

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

Every token processed is shown, along with its ID, type and contents. For each token, all sniffs that were executed on the token are displayed, along with the running time.

For example, the output above shows us that token 1, an if keyword, had 5 sniffs executed on it; the ControlSignature sniff, the MultiLineCondition sniff, the ScopeClosingBrace sniff, the ScopeIndent sniff and the InlineControlStructure sniff. Each was executed fairly quickly, but the slowest was the ControlSignature sniff, taking 0.0001 seconds to process that token.

The other interesting piece of information we get from the output above is that some tokens didn't have any sniffs executed on them. This is normal behaviour for PHP_CodeSniffer as most sniffs listen for specific or rarely used tokens and then execute on it and a number of tokens following it.

For example, the ScopeIndentSniff executes on the if statement's token only, but actually checks the indentation of every line within the if statement. The sniff uses the scope map to find all tokens within the if statement.

## Quieting Output
If a coding standard or configuration file includes settings to print progress or verbose output while running PHP_CodeSniffer, it can make it difficult to use the standard with automated checking tools and build scripts as these typically only expect an error report. If you have this problem, or just want less output, you can quieten the output of PHP_CodeSniffer by using the `-q` command line argument. When using this quiet mode, PHP_CodeSniffer will only print report output, and only if errors or warnings are found. No progress or verbose output will be printed.