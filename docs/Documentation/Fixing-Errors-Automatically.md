PHP_CodeSniffer is able to fix many errors and warnings automatically. The `diff` report can be used to generate a diff that can be applied using the `patch` command. Alternatively, the PHP Code Beautifier and Fixer (`phpcbf`) can be used in place of `phpcs` to automatically generate and apply the diff for you.

Screen-based reports, such as the [full](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting#printing-full-and-summary-reports), [summary](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting#printing-full-and-summary-reports) and [source](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting#printing-a-source-report) reports, provide information about how many errors and warnings are found. If any of the issues can be fixed automatically by `phpcbf`, additional information will be printed:

    $ phpcs /path/to/code/myfile.php

    FILE: /path/to/code/myfile.php
    --------------------------------------------------------------------------------
    FOUND 5 ERRORS AFFECTING 4 LINES
    --------------------------------------------------------------------------------
     2 | ERROR | [ ] Missing file doc comment
     3 | ERROR | [x] TRUE, FALSE and NULL must be lowercase; expected "false" but
       |       |     found "FALSE"
     5 | ERROR | [x] Line indented incorrectly; expected at least 4 spaces, found 1
     8 | ERROR | [ ] Missing function doc comment
     8 | ERROR | [ ] Opening brace should be on a new line
    --------------------------------------------------------------------------------
    PHPCBF CAN FIX THE 2 MARKED SNIFF VIOLATIONS AUTOMATICALLY
    --------------------------------------------------------------------------------

## Printing a Diff Report
PHP_CodeSniffer can output a diff file that can be applied using the `patch` command. The suggested changes will fix some of the sniff violations that are present in the source code. To print a diff report, use the `--report=diff` command line argument. The output will look like this:

    $ phpcs --report=diff /path/to/code
    
    --- /path/to/code/file.php
    +++ PHP_CodeSniffer
    @@ -1,8 +1,8 @@
     <?php
     
    -if ($foo === FALSE) {
    +if ($foo === false) {
    +    echo 'hi';
         echo 'hi';
    - echo 'hi';
     }
     
     function foo() {

Diff reports are more easily used when output to a file. They can then be applied using the `patch` command:

    $ phpcs --report-diff=/path/to/changes.diff /path/to/code
    $ patch -p0 -ui /path/to/changes.diff
    patching file /path/to/code/file.php

## Using the PHP Code Beautifier and Fixer
To automatically fix as many sniff violations as possible, use the `phpcbf` command in place of the `phpcs` command. While most of the PHPCS command line arguments can be used by PHPCBF, some are specific to reporting and will be ignored. Running PHPCBF with the `-h` or `--help` command line arguments will print a list of commands that PHPCBF will respond to. The output of `phpcbf -h` is shown below.
```
Usage: phpcbf [-nwli] [-d key[=value]] [--ignore-annotations] [--stdin-path=<stdinPath>]
  [--standard=<standard>] [--sniffs=<sniffs>] [--exclude=<sniffs>] [--suffix=<suffix>]
  [--severity=<severity>] [--error-severity=<severity>] [--warning-severity=<severity>]
  [--tab-width=<tabWidth>] [--encoding=<encoding>] [--parallel=<processes>]
  [--basepath=<basepath>] [--extensions=<extensions>] [--ignore=<patterns>] <file> - ...

 -     Fix STDIN instead of local files and directories
 -n    Do not fix warnings (shortcut for --warning-severity=0)
 -w    Fix both warnings and errors (on by default)
 -l    Local directory only, no recursion
 -p    Show progress of the run
 -q    Quiet mode; disables progress and verbose output
 -v    Print processed files
 -vv   Print ruleset and token output
 -vvv  Print sniff processing information
 -i    Show a list of installed coding standards
 -d    Set the [key] php.ini value to [value] or [true] if value is omitted

 --help                Print this help message
 --version             Print version information
 --ignore-annotations  Ignore all @codingStandard annotations in code comments

 <basepath>    A path to strip from the front of file paths inside reports
 <file>        One or more files and/or directories to fix
 <encoding>    The encoding of the files being fixed (default is utf-8)
 <extensions>  A comma separated list of file extensions to fix
               (extension filtering only valid when checking a directory)
               The type of the file can be specified using: ext/type
               e.g., module/php,es/js
 <patterns>    A comma separated list of patterns to ignore files and directories
 <processes>   How many files should be fixed simultaneously (default is 1)
 <severity>    The minimum severity required to fix an error or warning
 <sniffs>      A comma separated list of sniff codes to include or exclude from fixing
               (all sniffs must be part of the specified standard)
 <standard>    The name or path of the coding standard to use
 <stdinPath>   If processing STDIN, the file path that STDIN will be processed as
 <suffix>      Write modified files to a filename using this suffix
               ("diff" and "patch" are not used in this mode)
 <tabWidth>    The number of spaces each tab represents
```
When using the PHPCBF command, you do not need to specify a report type. PHPCBF will automatically make changes to your source files:

    $ phpcbf /path/to/code
    Processing init.php [PHP => 7875 tokens in 960 lines]... DONE in 274ms (12 fixable violations)
        => Fixing file: 0/12 violations remaining [made 3 passes]... DONE in 412ms
    Processing config.php [PHP => 8009 tokens in 957 lines]... DONE in 421ms (155 fixable violations)
        => Fixing file: 0/155 violations remaining [made 7 passes]... DONE in 937ms
    Patched 2 files
    Time: 2.55 secs, Memory: 25.00Mb

If you do not want to overwrite existing files, you can specify the `--suffix` command line argument and provide a filename suffix to use for new files. A fixed copy of each file will be created and stored in the same directory as the original file. If a file already exists with the new name, it will be overwritten.

    $ phpcbf /path/to/code --suffix=.fixed
    Processing init.php [PHP => 7875 tokens in 960 lines]... DONE in 274ms (12 fixable violations)
        => Fixing file: 0/12 violations remaining [made 3 passes]... DONE in 412ms
        => Fixed file written to init.php.fixed
    Processing config.php [PHP => 8009 tokens in 957 lines]... DONE in 421ms (155 fixable violations)
        => Fixing file: 0/155 violations remaining [made 7 passes]... DONE in 937ms
        => Fixed file written to config.php.fixed
    Fixed 2 files
    Time: 2.55 secs, Memory: 25.00Mb

## Viewing Debug Information

To see the fixes that are being made to a file, specify the `-vv` command line argument when generating a diff report. There is quite a lot of debug output concerning the standard being used and the tokenizing of the file, but the end of the output will look like this:

    $ phpcs /path/to/file --report=diff -vv
    ..snip..
    *** START FILE FIXING ***
    E: [Line 3] Expected 1 space after "="; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter)
    Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff (line 259) replaced token 4 (T_EQUAL) "=" => "=·"
    * fixed 1 violations, starting loop 2 *
    *** END FILE FIXING ***

Sometimes the file may need to be processed multiple times in order to fix all the violations. This can happen when multiple sniffs need to modify the same part of a file, or if a fix causes a new sniff violation somewhere else in the standard. When this happens, the output will look like this:

    $ phpcs /path/to/file --report=diff -vv
    ..snip..
    *** START FILE FIXING ***
    E: [Line 3] Expected 1 space before "="; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceBefore)
    Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff (line 228) replaced token 3 (T_EQUAL) "=" => "·="
    E: [Line 3] Expected 1 space after "="; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter)
    * token 3 has already been modified, skipping *
    E: [Line 3] Equals sign not aligned correctly; expected 1 space but found 0 spaces (Generic.Formatting.MultipleStatementAlignment.Incorrect)
    * token 3 has already been modified, skipping *
    * fixed 1 violations, starting loop 2 *
    E: [Line 3] Expected 1 space after "="; 0 found (Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter)
    Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff (line 259) replaced token 4 (T_EQUAL) "=" => "=·"
    * fixed 1 violations, starting loop 3 *
    *** END FILE FIXING ***