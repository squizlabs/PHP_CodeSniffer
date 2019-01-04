In this tutorial, we will create a new coding standard with a single sniff. Our sniff will prohibit the use of Perl style hash comments.

## Creating the Coding Standard Directory

All sniffs in PHP_CodeSniffer must belong to a coding standard. A coding standard is a directory with a specific sub-directory structure and a ruleset.xml file, so we can create one very easily. Let's call our coding standard _MyStandard_. Run the following commands to create the coding standard directory structure:

    $ mkdir MyStandard
    $ mkdir MyStandard/Sniffs

As this coding standard directory sits outside the main PHP_CodeSniffer directory structure, PHP_CodeSniffer will not show it as an installed standard when using the `-i` command line argument. If you want your standard to be shown as installed, create the MyStandard directory inside the PHP_CodeSniffer install:

    $ cd /path/to/PHP_CodeSniffer/src/Standards
    $ mkdir MyStandard
    $ mkdir MyStandard/Sniffs

The `MyStandard` directory represents our coding standard. The `Sniffs` sub-directory is used to store all the sniff files for this coding standard.

Now that our directory structure is created, we need to add our ruleset.xml file. This file will allow PHP_CodeSniffer to ask our coding standard for information about itself, and also identify this directory as one that contains code sniffs.

    $ cd MyStandard
    $ touch ruleset.xml

The content of the `ruleset.xml` file should be the following:

```xml
<?xml version="1.0"?>
<ruleset name="MyStandard">
  <description>A custom coding standard.</description>
</ruleset>
```

> The ruleset.xml can be left quite small, as it is in this example coding standard. For information about the other features that the ruleset.xml provides, see the [[Annotated ruleset]].

## Creating the Sniff

A sniff requires a single PHP file that must be placed into a sub-directory to categorise the type of check it performs. It's name should clearly describe the standard that we are enforcing and must end with `Sniff.php`. For our sniff, we will name the PHP file `DisallowHashCommentsSniff.php` and place it into a `Commenting` sub-directory to categorise this sniff as relating to commenting. Run the following commands to create the category and the sniff:

    $ cd Sniffs
    $ mkdir Commenting
    $ touch Commenting/DisallowHashCommentsSniff.php

> It does not matter what sub-directories you use for categorising your sniffs. Just make them descriptive enough so you can find your sniffs again later when you want to modify them.

Each sniff must implement the `PHP_CodeSniffer\Sniffs\Sniff` interface so that PHP_CodeSniffer knows that it should instantiate the sniff once it's invoked. The interface defines two methods that must be implemented; `register` and `process`.

## The `register` and `process` Methods

The `register` method allows a sniff to subscribe to one or more token types that it wants to process. Once PHP_CodeSniffer encounters one of those tokens, it calls the `process` method with the `PHP_CodeSniffer\Files\File` object (a representation of the current file being checked) and the position in the stack where the token was found.

For our sniff, we are interested in single line comments. The `token_get_all` method that PHP_CodeSniffer uses to acquire the tokens within a file distinguishes doc comments and normal comments as two separate token types. Therefore, we don't have to worry about doc comments interfering with our test. The `register` method only needs to return one token type, `T_COMMENT`.

## The Token Stack

A sniff can gather more information about a token by acquiring the token stack with a call to the `getTokens` method on the `PHP_CodeSniffer\Files\File` object. This method returns an array and is indexed by the position where the token occurs in the token stack. Each element in the array represents a token. All tokens have a `code`, `type` and a `content` index in their array. The `code` value is a unique integer for the type of token. The `type` value is a string representation of the token (e.g., 'T_COMMENT' for comment tokens). The `type` has a corresponding globally defined integer with the same name. Finally, the `content` value contains the content of the token as it appears in the code.

## Reporting Errors

Once an error is detected, a sniff should indicate that an error has occurred by calling the `addError` method on the `PHP_CodeSniffer\Files\File` object, passing in an appropriate error message as the first argument, the position in the stack where the error was detected as the second, a code to uniquely identify the error within this sniff and an array of data used inside the error message. Alternatively, if the violation is considered not as critical as an error, the `addWarning` method can be used.

## DisallowHashCommentsSniff.php

We now have to write the content of our sniff. The content of the `DisallowHashCommentsSniff.php` file should be the following:

```php
<?php
/**
 * This sniff prohibits the use of Perl style hash comments.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Your Name <you@domain.net>
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

namespace PHP_CodeSniffer\Standards\MyStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowHashCommentsSniff implements Sniff
{


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_COMMENT);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if ($tokens[$stackPtr]['content']{0} === '#') {
            $error = 'Hash comments are prohibited; found %s';
            $data  = array(trim($tokens[$stackPtr]['content']));
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }

    }//end process()


}//end class

?>
```

By default, PHP_CodeSniffer assumes all sniffs are designed to check PHP code only. You can specify a list of tokenizers that your sniff supports, allowing it to be used wth PHP, JavaScript or XML files, or any combination of the three. You do this by setting the `$supportedTokenizers` member variable in your sniff. Adding the following code to your sniff will tell PHP_CodeSniffer that it can be used to check both PHP and JavaScript code:

```php
/**
 * A list of tokenizers this sniff supports.
 *
 * @var array
 */
public $supportedTokenizers = array(
                               'PHP',
                               'JS',
                              );
```


## Results

Now that we have defined a coding standard, let's validate a file that contains hash comments. The test file we are using has the following contents:

```php
<?php

# Check for valid contents.
if ($obj->contentsAreValid($array)) {
    $value = $obj->getValue();

    # Value needs to be an array.
    if (is_array($value) === false) {
        # Error.
        $obj->throwError();
        exit();
    }
}

?>
```

When PHP_CodeSniffer is run on the file using our new coding standard, 3 errors will be reported:

    $ phpcs --standard=/path/to/MyStandard test.php

    FILE: test.php
    --------------------------------------------------------------------------------
    FOUND 3 ERROR(S) AFFECTING 3 LINE(S)
    --------------------------------------------------------------------------------
     3 | ERROR | Hash comments are prohibited; found # Check for valid contents.
     7 | ERROR | Hash comments are prohibited; found # Value needs to be an array.
     9 | ERROR | Hash comments are prohibited; found # Error.
    --------------------------------------------------------------------------------

Note that we pass the absolute path to our coding standard directory on the command line because our standard is not installed inside the main PHP_CodeSniffer directory structure. If you have created your standard inside PHP_CodeSniffer, you can simply pass the name of the standard:

    $ phpcs --standard=MyStandard Test.php