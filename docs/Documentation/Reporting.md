## Table of contents
* [Printing Full and Summary Reports](#printing-full-and-summary-reports)
* Other Report Types
    * [Checkstyle](#printing-a-checkstyle-report)
    * [Code](#printing-a-code-report)
    * [CSV](#printing-a-csv-report)
    * Diff
    * [Emacs](#printing-an-emacs-report)
    * [Git Blame](#printing-a-git-blame-report)
    * HG Blame
    * [Information](#printing-an-information-report)
    * [JSON](#printing-a-json-report)
    * [JUnit](#printing-a-junit-report)
    * Notify-Send
    * [Source](#printing-a-source-report)
    * [SVN Blame](#printing-an-svn-blame-report)
    * [XML](#printing-an-xml-report)
* [Printing Multiple Reports](#printing-multiple-reports)
* [Running Interactively](#running-interactively)
* [Specifying a Report Width](#specifying-a-report-width)
* [Writing a Report to a File](#writing-a-report-to-a-file)

***

## Printing Full and Summary Reports
Both the full and summary reports can additionally show information about the source of errors and warnings. Source codes can be used with the `--sniffs` command line argument to only show messages from a specified list of sources. To include source codes in the report, use the `-s` command line argument.

    $ phpcs -s /path/to/code/myfile.php
    
    FILE: /path/to/code/classA.php
    --------------------------------------------------------------------------------
    FOUND 4 ERRORS AND 1 WARNING AFFECTING 5 LINES
    --------------------------------------------------------------------------------
      2 | ERROR   | [ ] Missing file doc comment
        |         |     (PEAR.Commenting.FileComment.Missing)
      4 | ERROR   | [x] TRUE, FALSE and NULL must be lowercase; expected "false" but
        |         |     found "FALSE" (Generic.PHP.LowerCaseConstant.Found)
      6 | ERROR   | [x] Line indented incorrectly; expected at least 4 spaces, found
        |         |     1 (PEAR.WhiteSpace.ScopeIndent.Incorrect)
      9 | ERROR   | [ ] Missing function doc comment
        |         |     (PEAR.Commenting.FunctionComment.Missing)
     11 | WARNING | [x] Inline control structures are discouraged
        |         |     (Generic.ControlStructures.InlineControlStructure.Discouraged)
    --------------------------------------------------------------------------------
    PHPCBF CAN FIX THE 3 MARKED SNIFF VIOLATIONS AUTOMATICALLY
    --------------------------------------------------------------------------------

    $ phpcs -s --report=summary /path/to/code
    
    PHP CODE SNIFFER REPORT SUMMARY
    --------------------------------------------------------------------------------
    FILE                                                            ERRORS  WARNINGS
    --------------------------------------------------------------------------------
    /path/to/code/classA.inc                                        5       0
    /path/to/code/classB.inc                                        1       1
    /path/to/code/classC.inc                                        0       2
    --------------------------------------------------------------------------------
    A TOTAL OF 6 ERROR(S) AND 3 WARNING(S) WERE FOUND IN 3 FILE(S)
    --------------------------------------------------------------------------------

## Printing a Source Report
PHP_CodeSniffer can output a summary report showing you the most common errors detected in your files so you can target specific parts of your coding standard for improvement. To print a source report, use the `--report=source` command line argument. The output will look like this:

    $ phpcs --report=source /path/to/code
    
    PHP CODE SNIFFER VIOLATION SOURCE SUMMARY
    -----------------------------------------------------------------------------
        STANDARD  CATEGORY            SNIFF                                 COUNT
    -----------------------------------------------------------------------------
    [x] PEAR      White space         Scope indent incorrect                1
    [x] Generic   PHP                 Lower case constant found             1
    [x] Generic   Control structures  Inline control structure discouraged  1
    [ ] PEAR      Commenting          Function comment missing              1
    [ ] PEAR      Commenting          File comment missing                  1
    -----------------------------------------------------------------------------
    A TOTAL OF 5 SNIFF VIOLATIONS WERE FOUND IN 5 SOURCES
    -----------------------------------------------------------------------------
    PHPCBF CAN FIX THE 3 MARKED SOURCES AUTOMATICALLY (3 VIOLATIONS IN TOTAL)
    -----------------------------------------------------------------------------

To show source codes instead of friendly names, use the `-s` command line argument.

    $ phpcs -s --report=source /path/to/code
    
    PHP CODE SNIFFER VIOLATION SOURCE SUMMARY
    -----------------------------------------------------------------------
        SOURCE                                                        COUNT
    -----------------------------------------------------------------------
    [x] Generic.ControlStructures.InlineControlStructure.Discouraged  1
    [x] PEAR.WhiteSpace.ScopeIndent.Incorrect                         1
    [x] Generic.PHP.LowerCaseConstant.Found                           1
    [ ] PEAR.Commenting.FunctionComment.Missing                       1
    [ ] PEAR.Commenting.FileComment.Missing                           1
    -----------------------------------------------------------------------
    A TOTAL OF 5 SNIFF VIOLATIONS WERE FOUND IN 5 SOURCES
    -----------------------------------------------------------------------
    PHPCBF CAN FIX THE 3 MARKED SOURCES AUTOMATICALLY (3 VIOLATIONS IN TOTAL)
    -----------------------------------------------------------------------

## Printing an Information Report
PHP_CodeSniffer can output an information report to show you how your code is written rather than checking that it conforms to a standard. This report will use one or more standards you pass to it and then use the sniffs within those standards to inspect your code. Sniffs must be written to support recording metrics for this feature, so not all sniffs will report back information. To print an information report, use the `--report=info` command line argument. The output will look like this:

    $ phpcs --report=info /path/to/code
    
    PHP CODE SNIFFER INFORMATION REPORT
    --------------------------------------------------------------------------------
    Class has doc comment: yes [10/10, 100%]

    Class opening brace placement: new line [10/10, 100%]

    Constant name case: upper [81/81, 100%]

    Control structure defined inline: no [863/863, 100%]

    EOL char: \n [10/10, 100%]

    File has doc comment: yes [10/10, 100%]

    Function has doc comment: yes [130/130, 100%]

    Function opening brace placement: new line [111/111, 100%]

    Inline comment style: // ... [585/594, 98.48%]
        /* ... */ => 9 (1.52%)

    Line indent: spaces [5099/5099, 100%]

    Line length: 80 or less [6723/7134, 94.24%]
        81-120 => 397 (5.56%)
        121-150 => 10 (0.14%)
        151 or more => 4 (0.06%)

    PHP constant case: lower [684/684, 100%]

    PHP short open tag used: no [10/10, 100%]

    Private method prefixed with underscore: yes [11/11, 100%]

    --------------------------------------------------------------------------------

When more than one variation is found for a particular coding convention, the most common variation is printed on the first line and the other variations that were found are indented on subsequent lines. Each convention is followed by a number and each variation followed by a percentage, indicating the number of times the convention was checked and the percentage of code using each variation.

In the example above, the `Inline comment style` convention was checked 594 times, indicating that 594 inline comments were found and checked. 585 of them (98.48%) used the `// ...` style variation and 9 of them (1.52%) used the `/* ... */` style variation.

> **Tip:** To check your code against a wide range of conventions, specify all included standards. This will take longer, but give you more information about your code: `phpcs --standard=Generic,PEAR,Squiz,PSR2,Zend --report=info /path/to/code`

## Printing a Code Report

PHP_CodeSniffer can output a report that shows a code snippet for each error and warning, showing the context in which the violation has occurred. The output will look like this:

    $ phpcs --report=code /path/to/code
    
    FILE: /path/to/code/classA.php
    ------------------------------------------------------------------------------------------------
    FOUND 4 ERRORS AND 1 WARNING AFFECTING 5 LINES
    ------------------------------------------------------------------------------------------------
    LINE  2: ERROR   [ ] Missing file doc comment
    ------------------------------------------------------------------------------------------------
        1:  <?php
    >>  2:
        3:  if·($foo·===·null)·{
        4:  ····$foo·=·FALSE;
    ------------------------------------------------------------------------------------------------
    LINE  4: ERROR   [x] TRUE, FALSE and NULL must be lowercase; expected "false" but found "FALSE"
    ------------------------------------------------------------------------------------------------
        2:
        3:  if·($foo·===·null)·{
    >>  4:  ····$foo·=·FALSE;
        5:  }·else·{
        6:  ·$foo·=·getFoo();
    ------------------------------------------------------------------------------------------------
    LINE  6: ERROR   [x] Line indented incorrectly; expected at least 4 spaces, found 1
    ------------------------------------------------------------------------------------------------
        4:  ····$foo·=·FALSE;
        5:  }·else·{
    >>  6:  ·$foo·=·getFoo();
        7:  }
        8:
    ------------------------------------------------------------------------------------------------
    LINE  9: ERROR   [ ] Missing function doc comment
    ------------------------------------------------------------------------------------------------
        7:  }
        8:
    >>  9:  function·getFoo()
       10:  {
       11:  ····if·($foo)·return·'foo';
    ------------------------------------------------------------------------------------------------
    LINE 11: WARNING [x] Inline control structures are discouraged
    ------------------------------------------------------------------------------------------------
        9:  function·getFoo()
       10:  {
    >> 11:  ····if·($foo)·return·'foo';
       12:  ····return·'bar';
       13:  }
    ------------------------------------------------------------------------------------------------
    PHPCBF CAN FIX THE 3 MARKED SNIFF VIOLATIONS AUTOMATICALLY
    ------------------------------------------------------------------------------------------------

**Note:** The code report shows up to 5 lines of source code for each violation, so it is best used when checking single files and short code snippets to ensure the report doesn't become unreadble due to its length.

## Printing a Checkstyle Report
PHP_CodeSniffer can output an XML report similar to the one produced by Checkstyle, allowing you to use the output in scripts and applications that already support Checkstyle. To print a Checkstyle report, use the `--report=checkstyle` command line argument. The output will look like this:

    $ phpcs --report=checkstyle /path/to/code
    
    <?xml version="1.0" encoding="UTF-8"?>
    <checkstyle version="x.x.x">
    <file name="/path/to/code/classA.php">
     <error line="2" column="1" severity="error" message="Missing file doc comment" source="PEAR.Commenting.FileComment.Missing"/>
     <error line="4" column="12" severity="error" message="TRUE, FALSE and NULL must be lowercase; expected &quot;false&quot; but found &quot;FALSE&quot;" source="Generic.PHP.LowerCaseConstant.Found"/>
     <error line="6" column="2" severity="error" message="Line indented incorrectly; expected at least 4 spaces, found 1" source="PEAR.WhiteSpace.ScopeIndent.Incorrect"/>
     <error line="9" column="1" severity="error" message="Missing function doc comment" source="PEAR.Commenting.FunctionComment.Missing"/>
     <error line="11" column="5" severity="warning" message="Inline control structures are discouraged" source="Generic.ControlStructures.InlineControlStructure.Discouraged"/>
    </file>
    </checkstyle>

## Printing a CSV Report
PHP_CodeSniffer can output a CSV report to allow you to parse the output easily and use the results in your own scripts. To print a CSV report, use the `--report=csv` command line argument. The output will look like this:

    $ phpcs --report=csv /path/to/code
    
    File,Line,Column,Type,Message,Source,Severity,Fixable
    "/path/to/code/classA.php",2,1,error,"Missing file doc comment",PEAR.Commenting.FileComment.Missing,5,0
    "/path/to/code/classA.php",4,12,error,"TRUE, FALSE and NULL must be lowercase; expected \"false\" but found \"FALSE\"",Generic.PHP.LowerCaseConstant.Found,5,1
    "/path/to/code/classA.php",6,2,error,"Line indented incorrectly; expected at least 4 spaces, found 1",PEAR.WhiteSpace.ScopeIndent.Incorrect,5,1
    "/path/to/code/classA.php",9,1,error,"Missing function doc comment",PEAR.Commenting.FunctionComment.Missing,5,0
    "/path/to/code/classA.php",11,5,warning,"Inline control structures are discouraged",Generic.ControlStructures.InlineControlStructure.Discouraged,5,1

**Note:** The first row of the CSV output defines the order of information. When using the CSV output, please parse this header row to determine the order correctly as the format may change over time or new information may be added.

## Printing an Emacs Report
PHP_CodeSniffer can output a report in a format the compiler built into the GNU Emacs text editor can understand. This lets you use the built-in complier to run PHP_CodeSniffer on a file you are editing and navigate between errors and warnings within the file. To print an Emacs report, use the `--report=emacs` command line argument. The output will look like this:

    $ phpcs --report=emacs /path/to/code
    
    /path/to/code/classA.php:2:1: error - Missing file doc comment
    /path/to/code/classA.php:4:12: error - TRUE, FALSE and NULL must be lowercase; expected "false" but found "FALSE"
    /path/to/code/classA.php:6:2: error - Line indented incorrectly; expected at least 4 spaces, found 1
    /path/to/code/classA.php:9:1: error - Missing function doc comment
    /path/to/code/classA.php:11:5: warning - Inline control structures are discouraged

To use PHP_CodeSniffer with Emacs, make sure you have installed PHP mode for Emacs. Then put the following into your .emacs file, changing PHP_CodeSniffer options as required.

    (require 'compile)
    (defun my-php-hook-function ()
     (set (make-local-variable 'compile-command) (format "phpcs --report=emacs --standard=PEAR %s" (buffer-file-name))))
    (add-hook 'php-mode-hook 'my-php-hook-function)

Now you can use the compile command and associated shortcuts to move between error messages within your file.

## Printing a Git Blame Report
PHP_CodeSniffer can make use of the `git blame` command to try and determine who committed each error and warning to a Git respository. To print a Git Blame report, use the `--report=gitblame` command line argument. The output will look like this:

    $ phpcs --report=gitblame /path/to/code
    
    PHP CODE SNIFFER GIT BLAME SUMMARY
    --------------------------------------------------------------------------------
    AUTHOR                                                              COUNT (%)
    --------------------------------------------------------------------------------
    jsmith                                                              51 (40.8)
    jblogs                                                              44 (30)
    pdeveloper                                                          43 (10.33)
    jscript                                                             27 (19.84)
    --------------------------------------------------------------------------------
    A TOTAL OF 165 SNIFF VIOLATION(S) WERE COMMITTED BY 4 AUTHOR(S)
    --------------------------------------------------------------------------------

Each author is listed with the number of violations they committed and the percentage of error lines to clean lines. The example report above shows that the developer `pdeveloper` has 43 violations but they only make up 10% of all code they have committed, while `jblogs` has 44 violations but they make up 30% of all their committed code. So these developers have about the same number of total violations, but `pdeveloper` seems to be doing a better job of conforming to the coding standard.

To show a breakdown of the types of violations each author is committing, use the `-s` command line argument.

    $ phpcs -s --report=gitblame /path/to/code
    
    PHP CODE SNIFFER GIT BLAME SUMMARY
    --------------------------------------------------------------------------------
    AUTHOR   SOURCE                                                     COUNT (%)
    --------------------------------------------------------------------------------
    jsmith                                                              51 (40.8)
             Squiz.Files.LineLength                                     47
             PEAR.Functions.FunctionCallSignature                       4
    jblogs                                                              44 (30)
             Squiz.Files.LineLength                                     40
             Generic.CodeAnalysis.UnusedFunctionParameter               2
             Squiz.CodeAnalysis.EmptyStatement                          1
             Squiz.Formatting.MultipleStatementAlignment                1
    --------------------------------------------------------------------------------
    A TOTAL OF 95 SNIFF VIOLATION(S) WERE COMMITTED BY 2 AUTHOR(S)
    --------------------------------------------------------------------------------

To include authors with no violations, use the `-v` command line argument.

    $ phpcs -v --report=gitblame /path/to/code
    
    PHP CODE SNIFFER GIT BLAME SUMMARY
    --------------------------------------------------------------------------------
    AUTHOR                                                              COUNT (%)
    --------------------------------------------------------------------------------
    jsmith                                                              51 (40.8)
    jblogs                                                              44 (30)
    pdeveloper                                                          43 (10.33)
    jscript                                                             27 (19.84)
    toogood                                                             0 (0)
    --------------------------------------------------------------------------------
    A TOTAL OF 165 SNIFF VIOLATION(S) WERE COMMITTED BY 5 AUTHOR(S)
    --------------------------------------------------------------------------------

**Note:** You need to make sure the location of the `git` command is in your path. If the command is not in your path, the report will fail to generate.

## Printing a JSON Report
PHP_CodeSniffer can output an JOSN report to allow you to parse the output easily and use the results in your own scripts. To print a JSON report, use the `--report=json` command line argument. The output will look like this:

    $ phpcs --report=json /path/to/code
    
    {
      "totals": {
        "errors": 4,
        "warnings": 1,
        "fixable": 3
      },
      "files": {
        "\/path\/to\/code\/classA.php": {
          "errors": 4,
          "warnings": 1,
          "messages": [
            {
              "message": "Missing file doc comment",
              "source": "PEAR.Commenting.FileComment.Missing",
              "severity": 5,
              "type": "ERROR",
              "line": 2,
              "column": 1,
              "fixable": false
            },
            {
              "message": "TRUE, FALSE and NULL must be lowercase; expected \"false\" but found \"FALSE\"",
              "source": "Generic.PHP.LowerCaseConstant.Found",
              "severity": 5,
              "type": "ERROR",
              "line": 4,
              "column": 12,
              "fixable": true
            },
            {
              "message": "Line indented incorrectly; expected at least 4 spaces, found 1",
              "source": "PEAR.WhiteSpace.ScopeIndent.Incorrect",
              "severity": 5,
              "type": "ERROR",
              "line": 6,
              "column": 2,
              "fixable": true
            },
            {
              "message": "Missing function doc comment",
              "source": "PEAR.Commenting.FunctionComment.Missing",
              "severity": 5,
              "type": "ERROR",
              "line": 9,
              "column": 1,
              "fixable": false
            },
            {
              "message": "Inline control structures are discouraged",
              "source": "Generic.ControlStructures.InlineControlStructure.Discouraged",
              "severity": 5,
              "type": "WARNING",
              "line": 11,
              "column": 5,
              "fixable": true
            }
          ]
        },
        "\/path\/to\/code\/classB.php": {
          "errors": 0,
          "warnings": 0,
          "messages": [
            
          ]
        }
      }
    }

## Printing a JUnit Report
PHP_CodeSniffer can output an XML report similar to the one produced by JUnit, allowing you to use the output in scripts and applications that already support JUnit. To print a JUnit report, use the `--report=junit` command line argument. The output will look like this:

    $ phpcs --report=junit /path/to/code
    
    <?xml version="1.0" encoding="UTF-8"?>
    <testsuites name="PHP_CodeSniffer x.x.x" tests="6" failures="5">
    <testsuite name="/path/to/code/classA.php" tests="5" failures="5">
     <testcase name="PEAR.Commenting.FileComment.Missing at /path/to/code/classA.php (2:1)">
      <failure type="error" message="Missing file doc comment"/>
     </testcase>
     <testcase name="Generic.PHP.LowerCaseConstant.Found at /path/to/code/classA.php (4:12)">
      <failure type="error" message="TRUE, FALSE and NULL must be lowercase; expected &quot;false&quot; but found &quot;FALSE&quot;"/>
     </testcase>
     <testcase name="PEAR.WhiteSpace.ScopeIndent.Incorrect at /path/to/code/classA.php (6:2)">
      <failure type="error" message="Line indented incorrectly; expected at least 4 spaces, found 1"/>
     </testcase>
     <testcase name="PEAR.Commenting.FunctionComment.Missing at /path/to/code/classA.php (9:1)">
      <failure type="error" message="Missing function doc comment"/>
     </testcase>
     <testcase name="Generic.ControlStructures.InlineControlStructure.Discouraged at /path/to/code/classA.php (11:5)">
      <failure type="warning" message="Inline control structures are discouraged"/>
     </testcase>
    </testsuite>
    <testsuite name="/path/to/code/classB.php" tests="1" failures="0">
     <testcase name="/path/to/code/classB.php"/>
    </testsuite>
    </testsuites>


## Printing an SVN Blame Report
Like the Git Blame report, PHP_CodeSniffer can make use of the `svn blame` command to try and determine who committed each error and warning to an SVN repository. To print an SVN Blame report, use the `--report=svnblame` command line argument. The output and options are the same as those described in the [Git Blame report](#printing-a-git-blame-report).

**Note:** You need to make sure the location of the `svn` command is in your path and that SVN is storing a username and password (if required by your repository). If the command is not in your path, the report will fail to generate. If SVN does not have a username and password stored, you'll need to enter it for each file being checked by PHP_CodeSniffer that contains violations.

## Printing an XML Report
PHP_CodeSniffer can output an XML report to allow you to parse the output easily and use the results in your own scripts. To print an XML report, use the `--report=xml` command line argument. The output will look like this:

    $ phpcs --report=xml /path/to/code
    
    <?xml version="1.0" encoding="UTF-8"?>
    <phpcs version="x.x.x">
    <file name="/path/to/code/classA.php" errors="4" warnings="1" fixable="3">
     <error line="2" column="1" source="PEAR.Commenting.FileComment.Missing" severity="5" fixable="0">Missing file doc comment</error>
     <error line="4" column="12" source="Generic.PHP.LowerCaseConstant.Found" severity="5" fixable="1">TRUE, FALSE and NULL must be lowercase; expected &quot;false&quot; but found &quot;FALSE&quot;</error>
     <error line="6" column="2" source="PEAR.WhiteSpace.ScopeIndent.Incorrect" severity="5" fixable="1">Line indented incorrectly; expected at least 4 spaces, found 1</error>
     <error line="9" column="1" source="PEAR.Commenting.FunctionComment.Missing" severity="5" fixable="0">Missing function doc comment</error>
     <warning line="11" column="5" source="Generic.ControlStructures.InlineControlStructure.Discouraged" severity="5" fixable="1">Inline control structures are discouraged</warning>
    </file>
    </phpcs>

## Printing Multiple Reports
PHP_CodeSniffer can print any combination of the above reports to either the screen or to separate files. To print multiple reports, use the `--report-[type]` command line argument instead of the standard `--report=[type]` format. You can then specify multiple reports using multiple arguments. The reports will be printed to the screen in the order you specify them on the command line.

The following command will write both a full and summary report to the screen

    $ phpcs --report-full --report-summary /path/to/code

You can write the reports to separate files by specifying the path to the output file after each report argument.

    $ phpcs --report-full=/path/to/full.txt --report-summary=/path/to/summary.txt /path/to/code

You can print some reports to the screen and other reports to files. The following command will write the full report to a file and a summary report to the screen.

    $ phpcs --report-full=/path/to/full.txt --report-summary /path/to/code

## Running Interactively
Instead of producing a single report at the end of a run, PHP_CodeSniffer can run interactively and show reports for files one at a time. When using the interactive mode, PHP_CodeSniffer will show a report for the first file it finds an error or warning in. It will then pause and wait for user input. Once you have corrected the errors, you can press `ENTER` to have PHP_CodeSniffer recheck your file and continue if the file is now free of errors. You can also choose to skip the file and move to the next file with errors.

To run PHP_CodeSniffer interactively, use the `-a` command line argument.

    $ phpcs -a /path/to/code
    
    FILE: /path/to/code/classA.php
    --------------------------------------------------------------------------------
    FOUND 4 ERRORS AND 1 WARNING AFFECTING 5 LINES
    --------------------------------------------------------------------------------
      2 | ERROR   | [ ] Missing file doc comment
      4 | ERROR   | [x] TRUE, FALSE and NULL must be lowercase; expected "false"
        |         |     but found "FALSE"
      6 | ERROR   | [x] Line indented incorrectly; expected at least 4 spaces,
        |         |     found 1
      9 | ERROR   | [ ] Missing function doc comment
     11 | WARNING | [x] Inline control structures are discouraged
    --------------------------------------------------------------------------------
    PHPCBF CAN FIX THE 3 MARKED SNIFF VIOLATIONS AUTOMATICALLY
    --------------------------------------------------------------------------------
    
    <ENTER> to recheck, [s] to skip or [q] to quit :

**Note:** PHP_CodeSniffer will always print the full error report for a file when running in interactive mode. Any report types you specify on the command line will be ignored.

## Specifying a Report Width
By default, PHP_CodeSniffer will print all screen-based reports 80 characters wide. File paths will be truncated if they don't fit within this limit and error messages will be wrapped across multiple lines. You can increase the report width to show longer file paths and limit the wrapping of error messages using the `--report-width` command line argument.

    $ phpcs --report-width=120 --report=summary /path/to/code/myfile.php

> Note: If you want reports to fill the entire terminal width (in supported terminals), set the `--report-width` command line argument to `auto`.
>
>    `$ phpcs --report-width=auto --report=summary /path/to/code/myfile.php`

## Writing a Report to a File
PHP_CodeSniffer always prints the specified report to the screen, but it can also be told to write a copy of the report to a file. When writing to a file, all internal parsing errors and verbose output PHP_CodeSniffer produces will not be included in the file. This feature is particularly useful when using report types such as XML and CSV that are often parsed by scripts or used with continuous integration software.

To write a copy of a report to a file, use the `--report-file` command line argument.

    $ phpcs --report=xml --report-file=/path/to/file.xml /path/to/code

**Note:** The report will not be written to the screen when using this option. If you still want to view the report, use the -v command line argument to print verbose output.