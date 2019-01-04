PHP_CodeSniffer version 3 contains a large number of core changes and breaks backwards compatibility for all custom sniffs and reports. The aim of this guide is to help developers upgrade their custom sniffs, unit tests, and reports from PHP_CodeSniffer version 2 to version 3.

> Note: If you only use the built-in coding standards, or you have a custom ruleset.xml file that only makes use of the sniffs and reports distributed with PHP_CodeSniffer, you do not need to make any changes to begin using PHP_CodeSniffer version 3.

***

## Table of contents
* [Upgrading Custom Sniffs](#upgrading-custom-sniffs)
    * [Extending Other Sniffs](#extending-other-sniffs)
    * [Extending the Included Abstract Sniffs](#extending-the-included-abstract-sniffs)
        * [AbstractVariableSniff](#abstractvariablesniff)
        * [AbstractPatternSniff](#abstractpatternsniff)
        * [AbstractScopeSniff](#abstractscopesniff)
* [New Class Names](#new-class-names)
    * [PHP_CodeSniffer_File](#php_codesniffer_file)
    * [PHP_CodeSniffer_Tokens](#php_codesniffer_tokens)
    * [PHP_CodeSniffer](#php_codesniffer)
* [Upgrading Unit Tests](#upgrading-unit-tests)
    * [Setting CLI Values](#setting-cli-values)
* [Upgrading Custom Reports](#upgrading-custom-reports)
    * [Supporting Concurrency](#supporting-concurrency)

***

## Upgrading Custom Sniffs

All sniffs must now be namespaced.

> Note: It doesn't matter what namespace you use for your sniffs as long as the last part of the namespace is in the format `StandardName\Sniffs\Category` as this is used to determine the sniff code. The examples below use a very minimal namespace but you can prefix it with whatever makes sense for your project. If you aren't sure what namespace to use, try using the example format.

> Note: If you decide to use a more complex prefix, or your prefix does not match the name of the directory containing your ruleset.xml file, you need to define the prefix in the ruleset tag of your ruleset.xml file. For example, if your namespace format for sniffs is `MyProject\CS\StandardName\Sniffs\Category`, set the namespace to `MyProject\CS\StandardName` (everything up to `\Sniffs\`). The ruleset tag would look like this: `<ruleset name="Custom Standard" namespace="MyProject\CS\StandardName">`

Internal namespace changes to core classes require changes to all sniff class definitions. The old definition looked like this:
```php
class StandardName_Sniffs_Category_TestSniff implements PHP_CodeSniffer_Sniff {}
```

The sniff class definition above should now be rewritten like this:
```php
namespace StandardName\Sniffs\Category;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class TestSniff implements Sniff {}
```

### Extending Other Sniffs

If your custom sniff extends another sniff, the class definition needs to change a bit more. Previously, a `class_exists()` call may have been used to autoload the sniff. Now, a `use` statement is used for autoloading, and the extended class name also changes.

The old class definition for a sniff extending another looked like this:
```php
if (class_exists('OtherStandardName_Sniffs_Category_TestSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class OtherStandardName_Sniffs_Category_TestSniff not found');
}

class StandardName_Sniffs_Category_TestSniff extends OtherStandardName_Sniffs_Category_TestSniff {}
```

The sniff class definition above should now be rewritten like this:
```php
namespace StandardName\Sniffs\Category;

use OtherStandardName\Sniffs\Category\TestSniff as OtherTestSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class TestSniff extends OtherTestSniff {}
```

### Extending the Included Abstract Sniffs

#### AbstractVariableSniff
If you previously extended the `AbstractVariableSniff`, your class definition will now look like this:
```php
namespace StandardName\Sniffs\Category;

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;

class TestSniff extends AbstractVariableSniff {}
```
#### AbstractPatternSniff
If you previously extended the `AbstractPatternSniff`, your class definition will now look like this:
```php
namespace StandardName\Sniffs\Category;

use PHP_CodeSniffer\Sniffs\AbstractPatternSniff;

class TestSniff extends AbstractPatternSniff {}
```
> Note: `PHP_CodeSniffer\Files\File` is not typically needed in a sniff that extends AbstractPatternSniff because these sniffs normally only override the `getPatterns()` method. If you are overriding a method that needs `File`, include the `use` statement as you would for any other sniff.

#### AbstractScopeSniff
If you previously extended the `AbstractScopeSniff`, your class definition will now look like this:
```php
namespace StandardName\Sniffs\Category;

use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Files\File;

class TestSniff extends AbstractScopeSniff {}
```

If you did not previously define the optional `processTokenOutsideScope()` method, you must now do so as it has been marked as abstract. Include the empty method below if you do not need to process tokens outside the specified scopes:
```php
protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
{
}
```

### New Class Names

#### PHP_CodeSniffer_File
Any references to `PHP_CodeSniffer_File` in your sniff should be changed to `File`. This includes the type hint that is normally used in the `process()` function definition. The old definition looked like this:
```php
public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {}
```

The `process()` function declaration should now be rewritten like this:
```php
public function process(File $phpcsFile, $stackPtr) {}
```

#### PHP_CodeSniffer_Tokens
If your sniff currently uses the `PHP_CodeSniffer_Tokens` class, you will need to add a use statement for `PHP_CodeSniffer\Util\Tokens` and then change references of `PHP_CodeSniffer_Tokens::` to `Tokens::` inside your sniff. The below example shows a sniff that is registering the list of comment tokens using the new `Tokens` class. Note the additional `use` statement:
```php
namespace StandardName\Sniffs\Category;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class TestSniff implements Sniff
{

    public function register()
    {
        return Tokens::$commentTokens;
    }

    public function process(File $phpcsFile, $stackPtr) {}

}
```

#### PHP_CodeSniffer
If your sniff currently uses the `PHP_CodeSniffer` class to access utility functions such as `isCamelCaps()` and `suggestType()`, you will need to add a use statement for `PHP_CodeSniffer\Util\Common` and then change references of `PHP_CodeSniffer::` to `Common::` inside your sniff. Your class definition will look like this:
```php
namespace StandardName\Sniffs\Category;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;

class TestSniff implements Sniff {}
```

## Upgrading Unit Tests

Internal namespace changes to core classes require changes to all unit test class definitions. The old definition looked like this:
```php
class StandardName_Tests_Category_TestSniffUnitTest implements AbstractSniffUnitTest {}
```

The unit test class definition above should now be rewritten like this:
```php
namespace StandardName\Tests\Category;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class TestSniffUnitTest extends AbstractSniffUnitTest {}
```

### Setting CLI Values

If your unit test class uses the `getCliValues()` method to specify CLI values to use during testing, you'll need to instead use the new `setCliValues()` method to set configuration values directly. A common use case for setting CLI values is to set the tab width, which was previously done using a method like this:
```php
public function getCliValues($testFile)
{
    return array('--tab-width=4');
}
```

Tab width is now set using this method:
```php
public function setCliValues($testFile, $config)
{
    $config->tabWidth = 4;
}
```
> Note: A complete list of configuration settings can be found in the documentation of the [Config class](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Config.php#L42).

## Upgrading Custom Reports

All reports must now be namespaced.

> Note: It doesn't really matter what namespace you use for your custom reports, but the examples below use a basic namespace based on the standard name. If you aren't sure what to use, try using this format.

Internal namespace changes to core classes require changes to all report class definitions. The old definition looked like this:
```php
class PHP_CodeSniffer_Reports_ReportName implements PHP_CodeSniffer_Report {}
```

The report class definition above should now be rewritten as this:
```php
namespace StandardName\Reports;

use PHP_CodeSniffer\Files\File;

class ReportName implements Report {}
```

The function signatures of the `generateFileReport()` and `generate()` methods are also slightly different. The `generateFileReport()` signature simply renames `PHP_CodeSniffer_File` to `File` due to namespace changes, while the `generate()` signature adds a new `$interactive` argument so reports know if PHP_CodeSniffer is running in interactive mode. This is useful so that reports can suppress output such as memory and time usage when they know they are printing in this mode, or even change their output completely as they know they are only printing a report for a single file.

The old method signatures looked like this:
```php
public function generateFileReport(
    $report,
    PHP_CodeSniffer_File $phpcsFile,
    $showSources=false,
    $width=80
) {
    ...
}

public function generate(
    $cachedData,
    $totalFiles,
    $totalErrors,
    $totalWarnings,
    $totalFixable,
    $showSources=false,
    $width=80,
    $toScreen=true
) {
    ...
}
```

They should now be written like this:
```php
public function generateFileReport(
    $report,
    File $phpcsFile,
    $showSources=false,
    $width=80
) {
    ...
}

public function generate(
    $cachedData,
    $totalFiles,
    $totalErrors,
    $totalWarnings,
    $totalFixable,
    $showSources=false,
    $width=80,
    $interactive=false,
    $toScreen=true
) {
    ...
}
```

### Supporting Concurrency

PHP_CodeSniffer version 3 supports processing multiple files concurrently, so reports can no longer rely on getting file results one at a time. Reports that used to write to local member vars can no longer do so as multiple forks of the PHP_CodeSniffer process will all be writing to a different instance of the report class at the same time and these cache values will never be merged. Instead, reports need to output their cached data directly. They will later be given a chance to read in the entire cached output and generate a final clean report.

> Note: Reports that output content in a way where the order or formatting is not important do not need to worry about caching data and can continue to produce reports they way they do now. Examples of these reports include the CSV report and the XML report.

The Summary report is a good example of what changes need to be made. The summary report can't output a single final report line for each file it processes as it has to properly align all the values in the final screen report. Previously, it wrote the number of error and warnings found to a private member var array inside the `generateFileReport()` method and  later used that array to generate the final report. Even though it didn't output anything to screen, it had to return `true` to ensure the Reporter knew there were errors in the file:
```php
$this->_reportFiles[$report['filename']] = array(
                                            'errors'   => $report['errors'],
                                            'warnings' => $report['warnings'],
                                            'strlen'   => strlen($report['filename']),
                                           );
return true;
```

Now, it outputs cache information directly using a single line of output per file:
```php
echo $report['filename'].'>>'.$report['errors'].'>>'.$report['warnings'].PHP_EOL;
return true;
```

Previously, the Summary report would read it's private member var in the `generate()` method to get a list of all the cached data it has stored. It would then iterate over that data to generate the final report:
```php
if (empty($this->_reportFiles) === true) {
    return;
}

foreach ($this->_reportFiles as $file => $data) {
    ...
}
```

Now, it receives all the output the various forks of the PHP_CodeSniffer process produced in one big string. It explodes the data and then iterates over it as before:
```php
$lines = explode(PHP_EOL, $cachedData);
array_pop($lines);

if (empty($lines) === true) {
    return;
}

foreach ($lines as $line) {
    ...
}
```