# Changelog
The file documents changes to the PHP_CodeSniffer project.

## [Unreleased]

### Added
- An error message is now displayed if no files were checked during a run
    - This occurs when all of the specified files matched exclusion rules, or none matched filtering rules

### Changed
- The minimum required PHP version has changed from 5.4.0 to 7.2.0
- The default coding standard has changed from `PEAR` to `PSR12`
- Files with no extension are no longer ignored if the path is passed in directly
    - Previously, files with no extension would always be ignored
    - Now, files with no extension are checked if passed on the command line or specified in a ruleset
- The `--extensions` command line argument no longer accepts the tokenizer along with the extension
    - Previously, you would check `.module` files as PHP files using `--extensions=module/php`
    - Now, you use `--extensions=module`
- Rulesets now process their rules from top to bottom instead of in defined groups
    - Previously, rulesets processed tags in the following order, no matter where they appeared in the file:
        1. `<autoload>`
        2. `<config>`
        3. `<rule>`
        4. `<arg>`
        5. `<ini>`
        6. `<file>`
        7. `<exclude-pattern>`
    - Now, tags are processed as they are encountered when parsing the file top to bottom
- None of the included sniffs will warn about possible parse errors any more
    - This improves the experience when the file is being checked inside an editor during live coding
    - If you want to detect parse errors, use the `Generic.PHP.Syntax` sniff or a dedicated linter instead
- Changed the error code `Squiz.Classes.ValidClassName.NotCamelCaps` to `Squiz.Classes.ValidClassName.NotPascalCase`
    - This reflects that the sniff is actually checking for `ClassName` and not `className`
- All status, debug, and progress output is now sent to STDERR instead of STDOUT
    - Only report output now goes through STDOUT
    - Piping output to a file will now only include report output
        - Pipe both STDERR and STDOUT to the same file to capture the entire output of the run
    - The `--report-file` functionality remains untouched
- Composer installs no longer include any test files
- The `Config::setConfigData()` method is no longer static
- T_USE tokens now contain parenthesis information if they are being used to pass variables to a closure
    - Previously, you had to find the opening and closing parenthesis by looking forward through the token stack
    - Now, you can use the `parenthesis_opener` and `parenthesis_closer` array indexes

### Removed
- Removed support for installing via PEAR
    - Use composer or the phar files
- Support for checking the coding standards of JS files has been removed
- Support for checking the coding standards of CSS files has been removed
- Support for the deprecated `@codingStandard` annotation syntax has been removed
    - Use the `phpcs:` or `@phpcs:` syntax instead
        - Replace `@codingStandardsIgnoreFile` with `phpcs:ignoreFile`
        - Replace `@codingStandardsIgnoreStart` with `phpcs:disable`
        - Replace `@codingStandardsIgnoreEnd` with `phpcs:enable`
        - Replace `@codingStandardsIgnoreLine` with `phpcs:ignore`
        - Replace `@codingStandardsChangeSetting` with `phpcs:set`
- Support for the deprecated `ruleset.xml` array property string-based syntax has been removed
    - Previously, setting an array value used the string syntax `print=>echo,create_function=>null`
    - Now, individual array elements are specified using an `element` tag with `key` and `value` attributes
        - For example, `<element key="print" value="echo">`
- Removed the unused `T_ARRAY_HINT` token
- Removed the unused `T_RETURN_TYPE` token
- Removed JS-specific sniff `Generic.Debug.ClosureLinter`
- Removed CSS-specific sniff `Generic.Debug.CSSLint`
- Removed JS-specific sniff `Generic.Debug.ESLint`
- Removed JS-specific sniff `Generic.Debug.JSHint`
- Removed JS-specific sniff `Squiz.Classes.DuplicateProperty`
- Removed JS-specific sniff `Squiz.Debug.JavaScriptLint`
- Removed JS-specific sniff `Squiz.Debug.JSLint`
- Removed JS-specific sniff `Squiz.Objects.DisallowObjectStringIndex`
- Removed JS-specific sniff `Squiz.Objects.ObjectMemberComment`
- Removed deprecated sniff `Squiz.WhiteSpace.LanguageConstructSpacing`
    - Use `Generic.WhiteSpace.LanguageConstructSpacing` instead
- Removed JS-specific sniff `Squiz.WhiteSpace.PropertyLabelSpacing`
- Removed the entire `Squiz.CSS` category, and all sniffs within
- Removed the entire `MySource` standard, and all sniffs within
- Removed `error` property of sniff `Generic.Strings.UnnecessaryStringConcat`
    - This sniff now always produces errors
    - To make this sniff produce warnings, include the following in a `ruleset.xml` file:
        ```xml
        <rule ref="Generic.Strings.UnnecessaryStringConcat">
            <type>warning</type>
        </rule>
        ```
- Removed `error` property of sniff `Generic.Formatting.MultipleStatementAlignment`
    - This sniff now always produces warnings
    - Also removes the `Generic.Formatting.MultipleStatementAlignment.IncorrectWarning` sniff message
        - Now renamed to `Generic.Formatting.MultipleStatementAlignment.Incorrect`
    - Also removes the `Generic.Formatting.MultipleStatementAlignment.NotSameWarning` sniff message
        - Now renamed to `Generic.Formatting.MultipleStatementAlignment.NotSame`
    - To make this sniff produce errors, include the following in a `ruleset.xml` file:
        ```xml
        <rule ref="Generic.Formatting.MultipleStatementAlignment">
            <type>error</type>
        </rule>
        ```

## [3.5.6] - 2020-08-10
### Added
- Added support for PHP 8.0 magic constant dereferencing
    - Thanks to Juliette Reinders Folmer for the patch
- Added support for changes to the way PHP 8.0 tokenizes comments
    - The existing PHP 5-7 behaviour has been replicated for version 8, so no sniff changes are required
    - Thanks to Juliette Reinders Folmer for the patch
- `File::getMethodProperties()` now detects the PHP 8.0 static return type
    - Thanks to Juliette Reinders Folmer for the patch
- The PHP 8.0 static return type is now supported for arrow functions
    - Thanks to Juliette Reinders Folmer for the patch

### Changed
- The cache is no longer used if the list of loaded PHP extensions changes
    - Thanks to Juliette Reinders Folmer for the patch
- `Generic.NamingConventions.CamelCapsFunctionName` no longer reports `__serialize` and `__unserialize` as invalid names
    - Thanks to Filip Š for the patch
- `PEAR.NamingConventions.ValidFunctionName` no longer reports `__serialize` and `__unserialize` as invalid names
    - Thanks to Filip Š for the patch
- `Squiz.Scope.StaticThisUsage` now detects usage of `$this` inside closures and arrow functions
    - Thanks to Michał Bundyra for the patch

### Fixed
- Fixed bug #2877 : PEAR.Functions.FunctionCallSignature false positive for array of functions
    - Thanks to Vincent Langlet for the patch
- Fixed bug #2888 : PSR12.Files.FileHeader blank line error with multiple namespaces in one file
- Fixed bug #2926 : phpcs hangs when using arrow functions that return heredoc
- Fixed bug #2943 : Redundant semicolon added to a file when fixing PSR2.Files.ClosingTag.NotAllowed
- Fixed bug #2967 : Markdown generator does not output headings correctly
    - Thanks to Petr Bugyík for the patch
- Fixed bug #2977 : File::isReference() does not detect return by reference for closures
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2994 : Generic.Formatting.DisallowMultipleStatements false positive for FOR loop with no body
- Fixed bug #3033 : Error generated during tokenizing of goto statements on PHP 8
    - Thanks to Juliette Reinders Folmer for the patch

## [3.5.5] - 2020-04-17
### Changed
- The T_FN backfill now works more reliably so T_FN tokens only ever represent real arrow functions
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed an issue where including sniffs using paths containing multiple dots would silently fail
- Generic.CodeAnalysis.EmptyPHPStatement now detects empty statements at the start of control structures

### Fixed
- Error wording in PEAR.Functions.FunctionCallSignature now always uses "parenthesis" instead of sometimes using "bracket"
    - Thanks to Vincent Langlet for the patch
- Fixed bug #2787 : Squiz.PHP.DisallowMultipleAssignments not ignoring typed property declarations
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2810 : PHPCBF fails to fix file with empty statement at start on control structure
- Fixed bug #2812 : Squiz.Arrays.ArrayDeclaration not detecting some arrays with multiple arguments on the same line
    - Thanks to Jakub Chábek for the patch
- Fixed bug #2826 : Generic.WhiteSpace.ArbitraryParenthesesSpacing doesn't detect issues for statements directly after a   control structure
    - Thanks to Vincent Langlet for the patch
- Fixed bug #2848 : PSR12.Files.FileHeader false positive for file with mixed PHP and HTML and no file header
- Fixed bug #2849 : Generic.WhiteSpace.ScopeIndent false positive with arrow function inside array
- Fixed bug #2850 : Generic.PHP.LowerCaseKeyword complains __HALT_COMPILER is uppercase
- Fixed bug #2853 : Undefined variable error when using Info report
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2865 : Double arrow tokenized as T_STRING when placed after function named "fn"
- Fixed bug #2867 : Incorrect scope matching when arrow function used inside IF condition
- Fixed bug #2868 : phpcs:ignore annotation doesnt work inside a docblock
- Fixed bug #2878 : PSR12.Files.FileHeader conflicts with Generic.Files.LineEndings
- Fixed bug #2895 : PSR2.Methods.FunctionCallSignature.MultipleArguments false positive with arrow function argument

## [3.5.4] - 2020-01-31
### Changed
- The PHP 7.4 numeric separator backfill now works correctly for more float formats
    - Thanks to Juliette Reinders Folmer for the patch
- The PHP 7.4 numeric separator backfill is no longer run on PHP version 7.4.0 or greater
- File::getCondition() now accepts a 3rd argument that allows for the closest matching token to be returned
    - By default, it continues to return the first matched token found from the top of the file
- Fixed detection of array return types for arrow functions
- Added Generic.PHP.DisallowRequestSuperglobal to ban the use of the $_REQUEST superglobal
    - Thanks to Morerice for the contribution
- Generic.ControlStructures.InlineControlStructure no longer shows errors for WHILE and FOR statements without a body
    - Previously it required these to have curly braces, but there were no statements to enclose in them
    - Thanks to Juliette Reinders Folmer for the patch
- PSR12.ControlStructures.BooleanOperatorPlacement can now be configured to enforce a specific operator position
    - By default, the sniff ensures that operators are all at the begining or end of lines, but not a mix of both
    - Set the allowOnly property to "first" to enforce all boolean operators to be at the start of a line
    - Set the allowOnly property to "last" to enforce all boolean operators to be at the end of a line
    - Thanks to Vincent Langlet for the patch
- PSR12.Files.ImportStatement now auto-fixes import statements by removing the leading slash
    - Thanks to Michał Bundyra for the patch
- Squiz.ControlStructures.ForLoopDeclaration now has a setting to ignore newline characters
    - Default remains FALSE, so newlines are not allowed within FOR definitions
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
- Squiz.PHP.InnerFunctions now handles multiple nested anon classes correctly

### Fixed
- Fixed bug #2497 : Sniff properties not set when referencing a sniff using relative paths or non-native slashes
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2657 : Squiz.WhiteSpace.FunctionSpacing can remove spaces between comment and first/last method during auto-fixing
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2688 : Case statements not tokenized correctly when switch is contained within ternary
- Fixed bug #2698 : PHPCS throws errors determining auto report width when shell_exec is disabled
    - Thanks to Matthew Peveler for the patch
- Fixed bug #2730 : PSR12.ControlStructures.ControlStructureSpacing does not ignore comments between conditions
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2732 : PSR12.Files.FileHeader misidentifies file header in mixed content file
- Fixed bug #2745 : AbstractArraySniff wrong indices when mixed coalesce and ternary values
    - Thanks to Michał Bundyra for the patch
- Fixed bug #2748 : Wrong end of statement for fn closures
    - Thanks to Michał Bundyra for the patch
- Fixed bug #2751 : Autoload relative paths first to avoid confusion with files from the global include path
    - Thanks to Klaus Purer for the patch
- Fixed bug #2763 : PSR12 standard reports errors for multi-line FOR definitions
- Fixed bug #2768 : Generic.Files.LineLength false positive for non-breakable strings at exactly the soft limit
    - Thanks to Alex Miles for the patch
- Fixed bug #2773 : PSR2.Methods.FunctionCallSignature false positive when arrow function has array return type
- Fixed bug #2790 : PSR12.Traits.UseDeclaration ignores block comments
    - Thanks to Vincent Langlet for the patch
- Fixed bug #2791 : PSR12.Functions.NullableTypeDeclaration false positive when ternary operator used with instanceof
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2802 : Can't specify a report file path using the tilde shortcut
- Fixed bug #2804 : PHP4-style typed properties not tokenized correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2805 : Undefined Offset notice during live coding of arrow functions
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2843 : Tokenizer does not support alternative syntax for declare statements
    - Thanks to Juliette Reinders Folmer for the patch

## [3.5.3] - 2019-12-04
### Changed
- The PHP 7.4 T_FN token has been made available for older versions
    - T_FN represents the fn string used for arrow functions
    - The double arrow becomes the scope opener, and uses a new T_FN_ARROW token type
    - The token after the statement (normally a semicolon) becomes the scope closer
    - The token is also associated with the opening and closing parenthesis of the statement
    - Any functions named "fn" will cause have a T_FN token for the function name, but have no scope information
    - Thanks to Michał Bundyra for the help with this change
- PHP 7.4 numeric separators are now tokenized in the same way when using older PHP versions
    - Previously, a number like 1_000 would tokenize as T_LNUMBER (1), T_STRING (_000)
    - Now, the number tokenizes as T_LNUMBER (1_000)
    - Sniff developers should consider how numbers with underscores impact their custom sniffs
- The PHPCS file cache now takes file permissions into account
    - The cache is now invalidated for a file when its permissions are changed
- File::getMethodParameters() now supports arrow functions
- File::getMethodProperties() now supports arrow functions
- Added Fixer::changeCodeBlockIndent() to change the indent of a code block while auto-fixing
    - Can be used to either increase or decrease the indent
    - Useful when moving the start position of something like a closure, where you want the content to also move
- Added Generic.Files.ExecutableFile sniff
    - Ensures that files are not executable
    - Thanks to Matthew Peveler for the contribution
- Generic.CodeAnalysis.EmptyPhpStatement now reports unnecessary semicolons after control structure closing braces
    - Thanks to Vincent Langlet for the patch
- Generic.PHP.LowerCaseKeyword now enforces that the "fn" keyword is lowercase
    - Thanks to Michał Bundyra for the patch
- Generic.WhiteSpace.ScopeIndent now supports static arrow functions
- PEAR.Functions.FunctionCallSignature now adjusts the indent of function argument contents during auto-fixing
    - Previously, only the first line of an argument was changed, leading to inconsistent indents
    - This change also applies to PSR2.Methods.FunctionCallSignature
- PSR2.ControlStructures.ControlStructureSpacing now checks whitespace before the closing parenthesis of multi-line control structures
    - Previously, it incorrectly applied the whitespace check for single-line definitions only
- PSR12.Functions.ReturnTypeDeclaration now checks the return type of arrow functions
    - Thanks to Michał Bundyra for the patch
- PSR12.Traits.UseDeclaration now ensures all trait import statements are grouped together
    - Previously, the trait import section of the class ended when the first non-import statement was found
    - Checking now continues throughout the class to ensure all statements are grouped together
    - This also ensures that empty lines are not requested after an import statement that isn't the last one
- Squiz.Functions.LowercaseFunctionKeywords now enforces that the "fn" keyword is lowercase
    - Thanks to Michał Bundyra for the patch

### Fixed
- Fixed bug #2586 : Generic.WhiteSpace.ScopeIndent false positives when indenting open tags at a non tab-stop
- Fixed bug #2638 : Squiz.CSS.DuplicateClassDefinitionSniff sees comments as part of the class name
    - Thanks to Raphael Horber for the patch
- Fixed bug #2640 : Squiz.WhiteSpace.OperatorSpacing false positives for some negation operators
    - Thanks to Jakub Chábek and Juliette Reinders Folmer for the patch
- Fixed bug #2674 : Squiz.Functions.FunctionDeclarationArgumentSpacing prints wrong argument name in error message
- Fixed bug #2676 : PSR12.Files.FileHeader locks up when file ends with multiple inline comments
- Fixed bug #2678 : PSR12.Classes.AnonClassDeclaration incorrectly enforcing that closing brace be on a line by itself
- Fixed bug #2685 : File::getMethodParameters() setting typeHintEndToken for vars with no type hint
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2694 : AbstractArraySniff produces invalid indices when using ternary operator
    - Thanks to Michał Bundyra for the patch
- Fixed bug #2702 : Generic.WhiteSpace.ScopeIndent false positive when using ternary operator with short arrays

## [3.5.2] - 2019-10-28
### Changed
- Generic.ControlStructures.DisallowYodaConditions now returns less false positives
    - False positives were being returned for array comparisions, or when performing some function calls
- Squiz.WhiteSpace.SemicolonSpacing.Incorrect error message now escapes newlines and tabs
    - Provides a clearer error message as whitespace is now visible
    - Also allows for better output for report types such as CSV and XML
- The error message for PSR12.Files.FileHeader.SpacingAfterBlock has been made clearer
    - It now uses the wording from the published PSR-12 standard to indicate that blocks must be separated by a blank line
    - Thanks to Craig Duncan for the patch

### Fixed
- Fixed bug #2654 : Incorrect indentation for arguments of multiline function calls
- Fixed bug #2656 : Squiz.WhiteSpace.MemberVarSpacing removes comments before first member var during auto fixing
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2663 : Generic.NamingConventions.ConstructorName complains about old constructor in interfaces
- Fixed bug #2664 : PSR12.Files.OpenTag incorrectly identifies PHP file with only an opening tag
- Fixed bug #2665 : PSR12.Files.ImportStatement should not apply to traits
- Fixed bug #2673 : PSR12.Traits.UseDeclaration does not allow comments or blank lines between use statements


## [3.5.1] - 2019-10-17
### Changed
- Very very verbose diff report output has slightly changed to improve readability
    - Output is printed when running PHPCS with the --report=diff and -vvv command line arguments
    - Fully qualified class names have been replaced with sniff codes
    - Tokens being changed now display the line number they are on
- PSR2, PSR12, and PEAR standards now correctly check for blank lines at the start of function calls
    - This check has been missing from these standards, but has now been implemented
    - When using the PEAR standard, the error code is PEAR.Functions.FunctionCallSignature.FirstArgumentPosition
    - When using PSR2 or PSR12, the error code is PSR2.Methods.FunctionCallSignature.FirstArgumentPosition
- PSR12.ControlStructures.BooleanOperatorPlacement no longer complains when multiple expression appears on the same line
    - Previously, boolean operators were enforce to appear at the start or end of lines only
    - Boolean operators can now appear in the middle of the line
- PSR12.Files.FileHeader no longer ignores comments preceding a use, namespace, or declare statement
- PSR12.Files.FileHeader now allows a hashbang line at the top of the file

### Fixed
- Fixed bug #2506 : PSR2 standard can't auto fix multi-line function call inside a string concat statement
- Fixed bug #2530 : PEAR.Commenting.FunctionComment does not support intersection types in comments
- Fixed bug #2615 : Constant visibility false positive on non-class constants
- Fixed bug #2616 : PSR12.Files.FileHeader false positive when file only contains docblock
- Fixed bug #2619 : PSR12.Files.FileHeader locks up when inline comment is the last content in a file
- Fixed bug #2621 : PSR12.Classes.AnonClassDeclaration.CloseBraceSameLine false positive for anon class passed as function argument
    - Thanks to Martins Sipenko for the patch
- Fixed bug #2623 : PSR12.ControlStructures.ControlStructureSpacing not ignoring indentation inside multi-line string arguments
- Fixed bug #2624 : PSR12.Traits.UseDeclaration doesnt apply the correct indent during auto fixing
- Fixed bug #2626 : PSR12.Files.FileHeader detects @var annotations as file docblocks
- Fixed bug #2628 : PSR12.Traits.UseDeclaration does not allow comments above a USE declaration
- Fixed bug #2632 : Incorrect indentation of lines starting with "static" inside closures
- Fixed bug #2641 : PSR12.Functions.NullableTypeDeclaration false positive when using new static()

## [3.5.0] - 2019-09-27
### Changed
- The included PSR12 standard is now complete and ready to use
    - Check your code using PSR-12 by running PHPCS with --standard=PSR12
- Added support for PHP 7.4 typed properties
    - The nullable operator is now tokenized as T_NULLABLE inside property types, as it is elsewhere
    - To get the type of a member var, use the File::getMemberProperties() method, which now contains a "type" array index
        - This contains the type of the member var, or a blank string if not specified
        - If the type is nullable, the return type will contain the leading ?
        - If a type is specified, the position of the first token in the type will be set in a "type_token" array index
        - If a type is specified, the position of the last token in the type will be set in a "type_end_token" array index
        - If the type is nullable, a "nullable_type" array index will also be set to TRUE
        - If the type contains namespace information, it will be cleaned of whitespace and comments in the return value
- The PSR1 standard now correctly bans alternate PHP tags
    - Previously, it only banned short open tags and not the pre-7.0 alternate tags
- Added support for only checking files that have been locally staged in a git repo
    - Use --filter=gitstaged to check these files
    - You still need to give PHPCS a list of files or directories in which to apply the filter
    - Thanks to Juliette Reinders Folmer for the contribution
- JSON reports now end with a newline character
- The phpcs.xsd schema now validates phpcs-only and phpcbf-only attributes correctly
    - Thanks to Juliette Reinders Folmer for the patch
- The tokenizer now correctly identifies inline control structures in more cases
- All helper methods inside the File class now throw RuntimeException instead of TokenizerException
    - Some tokenizer methods were also throwing RuntimeExpection but now correctly throw TokenizerException
    - Thanks to Juliette Reinders Folmer for the patch
- The File::getMethodParameters() method now returns more information, and supports closure USE groups
    - If a type hint is specified, the position of the last token in the hint will be set in a "type_hint_end_token" array index
    - If a default is specified, the position of the first token in the default value will be set in a "default_token" array   index
    - If a default is specified, the position of the equals sign will be set in a "default_equal_token" array index
    - If the param is not the last, the position of the comma will be set in a "comma_token" array index
    - If the param is passed by reference, the position of the reference operator will be set in a "reference_token" array index
    - If the param is variable length, the position of the variadic operator will be set in a "variadic_token" array index
- The T_LIST token and it's opening and closing parentheses now contain references to each other in the tokens array
    - Uses the same parenthesis_opener/closer/owner indexes as other tokens
    - Thanks to Juliette Reinders Folmer for the patch
- The T_ANON_CLASS token and it's opening and closing parentheses now contain references to each other in the tokens array
    - Uses the same parenthesis_opener/closer/owner indexes as other tokens
    - Only applicable if the anon class is passing arguments to the constructor
    - Thanks to Juliette Reinders Folmer for the patch
- The PHP 7.4 T_BAD_CHARACTER token has been made available for older versions
    - Allows you to safely look for this token, but it will not appear unless checking with PHP 7.4+
- Metrics are now available for Squiz.WhiteSpace.FunctionSpacing
    - Use the "info" report to see blank lines before/after functions
    - Thanks to Juliette Reinders Folmer for the patch
- Metrics are now available for Squiz.WhiteSpace.MemberVarSpacing
    - Use the "info" report to see blank lines before member vars
    - Thanks to Juliette Reinders Folmer for the patch
- Added Generic.ControlStructures.DisallowYodaConditions sniff
    - Ban the use of Yoda conditions
    - Thanks to Mponos George for the contribution
- Added Generic.PHP.RequireStrictTypes sniff
    - Enforce the use of a strict types declaration in PHP files
- Added Generic.WhiteSpace.SpreadOperatorSpacingAfter sniff
    - Checks whitespace between the spread operator and the variable/function call it applies to
    - Thanks to Juliette Reinders Folmer for the contribution
- Added PSR12.Classes.AnonClassDeclaration sniff
    - Enforces the formatting of anonymous classes
- Added PSR12.Classes.ClosingBrace sniff
    - Enforces that closing braces of classes/interfaces/traits/functions are not followed by a comment or statement
- Added PSR12.ControlStructures.BooleanOperatorPlacement sniff
    - Enforces that boolean operators between conditions are consistently at the start or end of the line
- Added PSR12.ControlStructures.ControlStructureSpacing sniff
    - Enforces that spacing and indents are correct inside control structure parenthesis
- Added PSR12.Files.DeclareStatement sniff
    - Enforces the formatting of declare statements within a file
- Added PSR12.Files.FileHeader sniff
    - Enforces the order and formatting of file header blocks
- Added PSR12.Files.ImportStatement sniff
    - Enforces the formatting of import statements within a file
- Added PSR12.Files.OpenTag sniff
    - Enforces that the open tag is on a line by itself when used at the start of a php-only file
- Added PSR12.Functions.ReturnTypeDeclaration sniff
    - Enforces the formatting of return type declarations in functions and closures
- Added PSR12.Properties.ConstantVisibility sniff
    - Enforces that constants must have their visibility defined
    - Uses a warning instead of an error due to this conditionally requiring the project to support PHP 7.1+
- Added PSR12.Traits.UseDeclaration sniff
    - Enforces the formatting of trait import statements within a class
- Generic.Files.LineLength ignoreComments property now ignores comments at the end of a line
    - Previously, this property was incorrectly causing the sniff to ignore any line that ended with a comment
    - Now, the trailing comment is not included in the line length, but the rest of the line is still checked
- Generic.Files.LineLength now only ignores unwrappable comments when the comment is on a line by itself
    - Previously, a short unwrappable comment at the end of the line would have the sniff ignore the entire line
- Generic.Functions.FunctionCallArgumentSpacing no longer checks spacing around assignment operators inside function calls
    - Use the Squiz.WhiteSpace.OperatorSpacing sniff to enforce spacing around assignment operators
        - Note that this sniff checks spacing around all assignment operators, not just inside function calls
    - The Generic.Functions.FunctionCallArgumentSpacing.NoSpaceBeforeEquals error has been removed
        - use Squiz.WhiteSpace.OperatorSpacing.NoSpaceBefore instead
    - The Generic.Functions.FunctionCallArgumentSpacing.NoSpaceAfterEquals error has been removed
        - use Squiz.WhiteSpace.OperatorSpacing.NoSpaceAfter instead
    - This also changes the PEAR/PSR2/PSR12 standards so they no longer check assignment operators inside function calls
        - They were previously checking these operators when they should not have
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.WhiteSpace.ScopeIndent no longer performs exact indents checking for chained method calls
    - Other sniffs can be used to enforce chained method call indent rules
    - Thanks to Pieter Frenssen for the patch
- PEAR.WhiteSpace.ObjectOperatorIndent now supports multi-level chained statements
    - When enabled, chained calls must be indented 1 level more or less than the previous line
    - Set the new "multilevel" setting to TRUE in a ruleset.xml file to enable this behaviour
    - Thanks to Marcos Passos for the patch
- PSR2.ControlStructures.ControlStructureSpacing now allows whitespace after the opening parenthesis if followed by a comment
    - Thanks to Michał Bundyra for the patch
- PSR2.Classes.PropertyDeclaration now enforces a single space after a property type keyword
    - The PSR2 standard itself excludes this new check as it is not defined in the written standard
    - Using the PSR12 standard will enforce this check
- Squiz.Commenting.BlockComment no longer requires blank line before comment if it's the first content after the PHP open tag
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Functions.FunctionDeclarationArgumentSpacing now has more accurate error messages
    - This includes renaming the SpaceAfterDefault error code to SpaceAfterEquals, which reflects the real error
- Squiz.Functions.FunctionDeclarationArgumentSpacing now checks for no space after a reference operator
    - If you don't want this new behaviour, exclude the SpacingAfterReference error message in a ruleset.xml file
- Squiz.Functions.FunctionDeclarationArgumentSpacing now checks for no space after a variadic operator
    - If you don't want this new behaviour, exclude the SpacingAfterVariadic error message in a ruleset.xml file
- Squiz.Functions.MultiLineFunctionDeclaration now has improved fixing for the FirstParamSpacing and UseFirstParamSpacing errors
- Squiz.Operators.IncrementDecrementUsage now suggests pre-increment of variables instead of post-increment
    - This change does not enforce pre-increment over post-increment; only the suggestion has changed
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.PHP.DisallowMultipleAssignments now has a second error code for when assignments are found inside control structure   conditions
    - The new error code is Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
    - All other multiple assignment cases use the existing error code Squiz.PHP.DisallowMultipleAssignments.Found
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.FunctionSpacing now applies beforeFirst and afterLast spacing rules to nested functions
    - Previously, these rules only applied to the first and last function in a class, interface, or trait
    - These rules now apply to functions nested in any statement block, including other functions and conditions
- Squiz.WhiteSpace.OperatorSpacing now has improved handling of parse errors
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.OperatorSpacing now checks spacing around the instanceof operator
    - Thanks to Jakub Chábek for the patch
- Squiz.WhiteSpace.OperatorSpacing can now enforce a single space before assignment operators
    - Previously, the sniff this spacing as multiple assignment operators are sometimes aligned
    - Now, you can set the ignoreSpacingBeforeAssignments sniff property to FALSE to enable checking
    - Default remains TRUE, so spacing before assignments is not checked by default
    - Thanks to Jakub Chábek for the patch

### Fixed
- Fixed bug #2391 : Sniff-specific ignore rules inside rulesets are filtering out too many files
    - Thanks to Juliette Reinders Folmer and Willington Vega for the patch
- Fixed bug #2478 : FunctionCommentThrowTag.WrongNumber when exception is thrown once but built conditionally
- Fixed bug #2479 : Generic.WhiteSpace.ScopeIndent error when using array destructing with exact indent checking
- Fixed bug #2498 : Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed autofix breaks heredoc
- Fixed bug #2502 : Generic.WhiteSpace.ScopeIndent false positives with nested switch indentation and case fall-through
- Fixed bug #2504 : Generic.WhiteSpace.ScopeIndent false positives with nested arrays and nowdoc string
- Fixed bug #2511 : PSR2 standard not checking if closing paren of single-line function declaration is on new line
- Fixed bug #2512 : Squiz.PHP.NonExecutableCode does not support alternate SWITCH control structure
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2522 : Text generator throws error when code sample line is too long
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2526 : XML report format has bad syntax on Windows
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2529 : Generic.Formatting.MultipleStatementAlignment wrong error for assign in string concat
- Fixed bug #2534 : Unresolvable installed_paths can lead to open_basedir errors
    - Thanks to Oliver Nowak for the patch
- Fixed bug #2541 : Text doc generator does not allow for multi-line rule explanations
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2549 : Searching for a phpcs.xml file can throw warnings due to open_basedir restrictions
    - Thanks to Matthew Peveler for the patch
- Fixed bug #2558 : PHP 7.4 throwing offset syntax with curly braces is deprecated message
    - Thanks to Matthew Peveler for the patch
- Fixed bug #2561 : PHP 7.4 compatibility fix / implode argument order
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2562 : Inline WHILE triggers SpaceBeforeSemicolon incorrectly
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2565 : Generic.ControlStructures.InlineControlStructure confused by mixed short/long tags
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2566 : Author tag email validation doesn't support all TLDs
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2575 : Custom error messages don't have data replaced when cache is enabled
- Fixed bug #2601 : Squiz.WhiteSpace.FunctionSpacing incorrect fix when spacing is 0
- Fixed bug #2608 : PSR2 throws errors for use statements when multiple namespaces are defined in a file

## [3.4.2] - 2019-04-11
### Changed
- Squiz.Arrays.ArrayDeclaration now has improved handling of syntax errors

### Fixed
- Fixed an issue where the PCRE JIT on PHP 7.3 caused PHPCS to die when using the parallel option
    - PHPCS now disables the PCRE JIT before running
- Fixed bug #2368 : MySource.PHP.AjaxNullComparison throws error when first function has no doc comment
- Fixed bug #2414 : Indention false positive in switch/case/if combination
- Fixed bug #2423 : Squiz.Formatting.OperatorBracket.MissingBrackets error with static
- Fixed bug #2450 : Indentation false positive when closure containing nested IF conditions used as function argument
- Fixed bug #2452 : LowercasePHPFunctions sniff failing on "new \File()"
- Fixed bug #2453 : Squiz.CSS.SemicolonSpacingSniff false positive when style name proceeded by an asterisk
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2464 : Fixer conflict between Generic.WhiteSpace.ScopeIndent and Squiz.WhiteSpace.ScopeClosingBrace when class   indented 1 space
- Fixed bug #2465 : Excluding a sniff by path is not working
- Fixed bug #2467 : PHP open/close tags inside CSS files are replaced with internal PHPCS token strings when auto fixing

## [3.4.1] - 2019-03-19
### Changed
- The PEAR installable version of PHPCS was missing some files, which have been re-included in this release
    - The code report was not previously available for PEAR installs
    - The Generic.Formatting.SpaceBeforeCast sniff was not previously available for PEAR installs
    - The Generic.WhiteSpace.LanguageConstructSpacing sniff was not previously available for PEAR installs
    - Thanks to Juliette Reinders Folmer for the patch
- PHPCS will now refuse to run if any of the required PHP extensions are not loaded
    - Previously, PHPCS only relied on requirements being checked by PEAR and Composer
    - Thanks to Juliette Reinders Folmer for the patch
- Ruleset XML parsing errors are now displayed in a readable format so they are easier to correct
    - Thanks to Juliette Reinders Folmer for the patch
- The PSR2 standard no longer throws duplicate errors for spacing around FOR loop parentheses
    - Thanks to Juliette Reinders Folmer for the patch
- T_PHPCS_SET tokens now contain sniffCode, sniffProperty, and sniffPropertyValue indexes
    - Sniffs can use this information instead of having to parse the token content manually
- Added more guard code for syntax errors to various CSS sniffs
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Commenting.DocComment error messages now contain the name of the comment tag that caused the error
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.ControlStructures.InlineControlStructure now handles syntax errors correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Debug.JSHint now longer requires rhino and can be run directly from the npm install
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Files.LineEndings no longer adds superfluous new line at the end of JS and CSS files
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Formatting.DisallowMultipleStatements no longer tries fix lines containing phpcs:ignore statements
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Functions.FunctionCallArgumentSpacing now has improved performance and anonymous class support
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.WhiteSpace.ScopeIndent now respects changes to the "exact" property using phpcs:set mid-way through a file
    - This allows you change the "exact" rule for only some parts of a file
- Generic.WhiteSpace.ScopeIndent now disables exact indent checking inside all arrays
    - Previously, this was only done when using long array syntax, but it now works for short array syntax as well
- PEAR.Classes.ClassDeclaration now has improved handling of PHPCS annotations and tab indents
- PSR12.Classes.ClassInstantiation has changed it's error code from MissingParenthesis to MissingParentheses
- PSR12.Keywords.ShortFormTypeKeywords now ignores all spacing inside type casts during both checking and fixing
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Classes.LowercaseClassKeywords now examines the class keyword for anonymous classes
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.ControlStructures.ControlSignature now has improved handling of parse errors
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.PostStatementComment fixer no longer adds a blank line at the start of a JS file that begins with a comment
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.SuperfluousWhitespace sniff
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.PostStatementComment now ignores comments inside control structure conditions, such as FOR loops
    - Fixes a conflict between this sniff and the Squiz.ControlStructures.ForLoopDeclaration sniff
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.FunctionCommentThrowTag now has improved support for unknown exception types and namespaces
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.ControlStructures.ForLoopDeclaration has improved whitespace, closure, and empty expression support
    - The SpacingAfterSecondNoThird error code has been removed as part of these fixes
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.CSS.ClassDefinitionOpeningBraceSpace now handles comments and indentation correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.CSS.ClassDefinitionClosingBrace now handles comments, indentation, and multiple statements on the same line correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.CSS.Opacity now handles comments correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.CSS.SemicolonSpacing now handles comments and syntax errors correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.NamingConventions.ValidVariableName now supports variables inside anonymous classes correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.PHP.LowercasePHPFunctions now handles use statements, namespaces, and comments correctly
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.FunctionSpacing now fixes function spacing correctly when a function is the first content in a file
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.SuperfluousWhitespace no longer throws errors for spacing between functions and properties in anon classes
    - Thanks to Juliette Reinders Folmer for the patch
- Zend.Files.ClosingTag no longer adds a semi-colon during fixing of a file that only contains a comment
    - Thanks to Juliette Reinders Folmer for the patch
- Zend.NamingConventions.ValidVariableName now supports variables inside anonymous classes correctly
    - Thanks to Juliette Reinders Folmer for the patch

### Fixed
- Fixed bug #2298 : PSR2.Classes.ClassDeclaration allows extended class on new line
    - Thanks to Michał Bundyra for the patch
- Fixed bug #2337 : Generic.WhiteSpace.ScopeIndent incorrect error when multi-line function call starts on same line as open   tag
- Fixed bug #2348 : Cache not invalidated when changing a ruleset included by another
- Fixed bug #2376 : Using __halt_compiler() breaks Generic.PHP.ForbiddenFunctions unless it's last in the function list
    - Thanks to Sijun Zhu for the patch
- Fixed bug #2393 : The gitmodified filter will infinitely loop when encountering deleted file paths
    - Thanks to Lucas Manzke for the patch
- Fixed bug #2396 : Generic.WhiteSpace.ScopeIndent incorrect error when multi-line IF condition mixed with HTML
- Fixed bug #2431 : Use function/const not tokenized as T_STRING when preceded by comment

## [3.4.0] - 2018-12-20
### Deprecated
- The Generic.Formatting.NoSpaceAfterCast sniff has been deprecated and will be removed in version 4
    - The functionality of this sniff is now available in the Generic.Formatting.SpaceAfterCast sniff
        - Include the Generic.Formatting.SpaceAfterCast sniff and set the "spacing" property to "0"
    - As soon as possible, replace all instances of the old sniff code with the new sniff code and property setting
        - The existing sniff will continue to work until version 4 has been released

### Changed
- Rule include patterns in a ruleset.xml file are now evaluated as OR instead of AND
    - Previously, a file had to match every include pattern and no exclude patterns to be included
    - Now, a file must match at least one include pattern and no exclude patterns to be included
    - This is a bug fix as include patterns are already documented to work this way
- New token T_BITWISE_NOT added for the bitwise not operator
    - This token was previously tokenized as T_NONE
    - Any sniffs specifically looking for T_NONE tokens with a tilde as the contents must now also look for T_BITWISE_NOT
    - Sniffs can continue looking for T_NONE as well as T_BITWISE_NOT to support older PHP_CodeSniffer versions
- All types of binary casting are now tokenized as T_BINARY_CAST
    - Previously, the 'b' in 'b"some string with $var"' would be a T_BINARY_CAST, but only when the string contained a var
    - This change ensures the 'b' is always tokenized as T_BINARY_CAST
    - This change also converts '(binary)' from T_STRING_CAST to T_BINARY_CAST
    - Thanks to Juliette Reinders Folmer for the help with this patch
- Array properties set inside a ruleset.xml file can now extend a previous value instead of always overwriting it
    - e.g., if you include a ruleset that defines forbidden functions, can you now add to that list instead of having to   redefine it
    - To use this feature, add extends="true" to the property tag
        - e.g., property name="forbiddenFunctionNames" type="array" extend="true"
    - Thanks to Michael Moravec for the patch
- If $XDG_CACHE_HOME is set and points to a valid directory, it will be used for caching instead of the system temp directory
- PHPCBF now disables parallel running if you are passing content on STDIN
    - Stops an error from being shown after the fixed output is printed
- The progress report now shows files with tokenizer errors as skipped (S) instead of a warning (W)
    - The tokenizer error is still displayed in reports as normal
    - Thanks to Juliette Reinders Folmer for the patch
- The Squiz standard now ensures there is no space between an increment/decrement operator and its variable
- The File::getMethodProperties() method now includes a has_body array index in the return value
    - FALSE if the method has no body (as with abstract and interface methods) or TRUE otherwise
    - Thanks to Chris Wilkinson for the patch
- The File::getTokensAsString() method now throws an exception if the $start param is invalid
    - If the $length param is invalid, an empty string will be returned
    - Stops an infinite loop when the function is passed invalid data
    - Thanks to Juliette Reinders Folmer for the patch
- Added new Generic.CodeAnalysis.EmptyPHPStatement sniff
    - Warns when it finds empty PHP open/close tag combinations or superfluous semicolons
    - Thanks to Juliette Reinders Folmer for the contribution
- Added new Generic.Formatting.SpaceBeforeCast sniff
    - Ensures there is exactly 1 space before a type cast, unless the cast statement is indented or multi-line
    - Thanks to Juliette Reinders Folmer for the contribution
- Added new Generic.VersionControl.GitMergeConflict sniff
    - Detects merge conflict artifacts left in files
    - Thanks to Juliette Reinders Folmer for the contribution
- Added Generic.WhiteSpace.IncrementDecrementSpacing sniff
    - Ensures there is no space between the operator and the variable it applies to
    - Thanks to Juliette Reinders Folmer for the contribution
- Added PSR12.Functions.NullableTypeDeclaration sniff
    - Ensures there is no space after the question mark in a nullable type declaration
    - Thanks to Timo Schinkel for the contribution
- A number of sniffs have improved support for methods in anonymous classes
    - These sniffs would often throw the same error twice for functions in nested classes
    - Error messages have also been changed to be less confusing
    - The full list of affected sniffs is:
        - Generic.NamingConventions.CamelCapsFunctionName
        - PEAR.NamingConventions.ValidFunctionName
        - PSR1.Methods.CamelCapsMethodName
        - PSR2.Methods.MethodDeclaration
        - Squiz.Scope.MethodScope
        - Squiz.Scope.StaticThisUsage
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter now only skips functions with empty bodies when the class implements an   interface
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter now has additional error codes to indicate where unused params were found
    - The new error code prefixes are:
        - FoundInExtendedClass: when the class extends another
        - FoundInImplementedInterface: when the class implements an interface
        - Found: used in all other cases, including closures
    - The new error code suffixes are:
        - BeforeLastUsed: the unused param was positioned before the last used param in the function signature
        - AfterLastUsed: the unused param was positioned after the last used param in the function signature
    - This makes the new error code list for this sniff:
        - Found
        - FoundBeforeLastUsed
        - FoundAfterLastUsed
        - FoundInExtendedClass
        - FoundInExtendedClassBeforeLastUsed
        - FoundInExtendedClassAfterLastUsed
        - FoundInImplementedInterface
        - FoundInImplementedInterfaceBeforeLastUsed
        - FoundInImplementedInterfaceAfterLastUsed
    - These errors code make it easier for specific cases to be ignored or promoted using a ruleset.xml file
    - Thanks to Juliette Reinders Folmer for the contribution
- Generic.Classes.DuplicateClassName now inspects traits for duplicate names as well as classes and interfaces
    - Thanks to Chris Wilkinson for the patch
- Generic.Files.InlineHTML now ignores a BOM at the start of the file
    - Thanks to Chris Wilkinson for the patch
- Generic.PHP.CharacterBeforePHPOpeningTag now ignores a BOM at the start of the file
    - Thanks to Chris Wilkinson for the patch
- Generic.Formatting.SpaceAfterCast now has a setting to specify how many spaces are required after a type cast
    - Default remains 1
    - Override the "spacing" setting in a ruleset.xml file to change
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Formatting.SpaceAfterCast now has a setting to ignore newline characters after a type cast
    - Default remains FALSE, so newlines are not allowed
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Formatting.SpaceAfterNot now has a setting to specify how many spaces are required after a NOT operator
    - Default remains 1
    - Override the "spacing" setting in a ruleset.xml file to change
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Formatting.SpaceAfterNot now has a setting to ignore newline characters after the NOT operator
    - Default remains FALSE, so newlines are not allowed
    - Override the "ignoreNewlines" setting in a ruleset.xml file to change
    - Thanks to Juliette Reinders Folmer for the patch
- PEAR.Functions.FunctionDeclaration now checks spacing before the opening parenthesis of functions with no body
    - Thanks to Chris Wilkinson for the patch
- PEAR.Functions.FunctionDeclaration now enforces no space before the semicolon in functions with no body
    - Thanks to Chris Wilkinson for the patch
- PSR2.Classes.PropertyDeclaration now checks the order of property modifier keywords
    - This is a rule that is documented in PSR-2 but was not enforced by the included PSR2 standard until now
    - This sniff is also able to fix the order of the modifier keywords if they are incorrect
    - Thanks to Juliette Reinders Folmer for the patch
- PSR2.Methods.MethodDeclaration now checks method declarations inside traits
    - Thanks to Chris Wilkinson for the patch
- Squiz.Commenting.InlineComment now has better detection of comment block boundaries
- Squiz.Classes.ClassFileName now checks that a trait name matches the filename
    - Thanks to Chris Wilkinson for the patch
- Squiz.Classes.SelfMemberReference now supports scoped declarations and anonymous classes
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Classes.SelfMemberReference now fixes multiple errors at once, increasing fixer performance
    - Thanks to Gabriel Ostrolucký for the patch
- Squiz.Functions.LowercaseFunctionKeywords now checks abstract and final prefixes, and auto-fixes errors
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Objects.ObjectMemberComma.Missing has been renamed to Squiz.Objects.ObjectMemberComma.Found
    - The error is thrown when the comma is found but not required, so the error code was incorrect
    - If you are referencing the old error code in a ruleset XML file, please use the new code instead
    - If you wish to maintain backwards compatibility, you can provide rules for both the old and new codes
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.ObjectOperatorSpacing is now more tolerant of parse errors
- Squiz.WhiteSpace.ObjectOperatorSpacing now fixes errors more efficiently
    - Thanks to Juliette Reinders Folmer for the patch

### Fixed
- Fixed bug #2109 : Generic.Functions.CallTimePassByReference false positive for bitwise and used in function argument
- Fixed bug #2165 : Conflict between Squiz.Arrays.ArrayDeclaration and ScopeIndent sniffs when heredoc used in array
- Fixed bug #2167 : Generic.WhiteSpace.ScopeIndent shows invalid error when scope opener indented inside inline HTML
- Fixed bug #2178 : Generic.NamingConventions.ConstructorName matches methods in anon classes with same name as containing   class
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2190 : PEAR.Functions.FunctionCallSignature incorrect error when encountering trailing PHPCS annotation
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2194 : Generic.Whitespace.LanguageConstructSpacing should not be checking namespace operators
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2202 : Squiz.WhiteSpace.OperatorSpacing throws error for negative index when using curly braces for string   access
    - Same issue fixed in Squiz.Formatting.OperatorBracket
    - Thanks to Andreas Buchenrieder for the patch
- Fixed bug #2210 : Generic.NamingConventions.CamelCapsFunctionName not ignoring SoapClient __getCookies() method
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2211 : PSR2.Methods.MethodDeclaration gets confused over comments between modifier keywords
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2212 : FUNCTION and CONST in use groups being tokenised as T_FUNCTION and T_CONST
    - Thanks to Chris Wilkinson for the patch
- Fixed bug #2214 : File::getMemberProperties() is recognizing method params as properties
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2236 : Memory info measurement unit is Mb but probably should be MB
- Fixed bug #2246 : CSS tokenizer does not tokenize class names correctly when they contain the string NEW
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2278 : Squiz.Operators.ComparisonOperatorUsage false positive when inline IF contained in parentheses
    - Thanks to Arnout Boks for the patch
- Fixed bug #2284 : Squiz.Functions.FunctionDeclarationArgumentSpacing removing type hint during fixing
    - Thanks to Michał Bundyra for the patch
- Fixed bug #2297 : Anonymous class not tokenized correctly when used as argument to another anon class
    - Thanks to Michał Bundyra for the patch

## [2.9.2] - 2018-11-08
### Changed
- PHPCS should now run under PHP 7.3 without deprecation warnings
    - Thanks to Nick Wilde for the patch

### Fixed
- Fixed bug #1496 : Squiz.Strings.DoubleQuoteUsage not unescaping dollar sign when fixing
    - Thanks to Michał Bundyra for the patch
- Fixed bug #1549 : Squiz.PHP.EmbeddedPhp fixer conflict with // comment before PHP close tag
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1890 : Incorrect Squiz.WhiteSpace.ControlStructureSpacing.NoLineAfterClose error between catch and finally statements

## [3.3.2] - 2018-09-24
### Changed
- Fixed a problem where the report cache was not being cleared when the sniffs inside a standard were updated
- The info report (--report=info) now has improved formatting for metrics that span multiple lines
    - Thanks to Juliette Reinders Folmer for the patch
- The unit test runner now skips .bak files when looking for test cases
    - Thanks to Juliette Reinders Folmer for the patch
- The Squiz standard now ensures underscores are not used to indicate visibility of private members vars and methods
    - Previously, this standard enforced the use of underscores
- Generic.PHP.NoSilencedErrors error messages now contain a code snippet to show the context of the error
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Arrays.ArrayDeclaration no longer reports errors for a comma on a line new after a here/nowdoc
    - Also stops a parse error being generated when auto-fixing
    - The SpaceBeforeComma error message has been changed to only have one data value instead of two
- Squiz.Commenting.FunctionComment no longer errors when trying to fix indents of multi-line param comments
- Squiz.Formatting.OperatorBracket now correctly fixes statements that contain strings
- Squiz.PHP.CommentedOutCode now ignores more @-style annotations and includes better comment block detection
    - Thanks to Juliette Reinders Folmer for the patch

### Fixed
- Fixed a problem where referencing a relative file path in a ruleset XML file could add unnecessary sniff exclusions
    - This didn't actually exclude anything, but caused verbose output to list strange exclusion rules
- Fixed bug #2110 : Squiz.WhiteSpace.FunctionSpacing is removing indents from the start of functions when fixing
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2115 : Squiz.Commenting.VariableComment not checking var types when the @var line contains a comment
- Fixed bug #2120 : Tokenizer fails to match T_INLINE_ELSE when used after function call containing closure
- Fixed bug #2121 : Squiz.PHP.DisallowMultipleAssignments false positive in while loop conditions
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2127 : File::findExtendedClassName() doesn't support nested classes
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2138 : Tokenizer detects wrong token for php ::class feature with spaces
- Fixed bug #2143 : PSR2.Namespaces.UseDeclaration does not properly fix "use function" and "use const" statements
    - Thanks to Chris Wilkinson for the patch
- Fixed bug #2144 : Squiz.Arrays.ArrayDeclaration does incorrect align calculation in array with cyrillic keys
- Fixed bug #2146 : Zend.Files.ClosingTag removes closing tag from end of file without inserting a semicolon
- Fixed bug #2151 : XML schema not updated with the new array property syntax

## [3.3.1] - 2018-07-27
### Removed
- Support for HHVM has been dropped due to recent unfixed bugs and HHVM refocus on Hack only
    - Thanks to Walt Sorensen and Juliette Reinders Folmer for helping to remove all HHVM exceptions from the core

### Changed
- The full report (the default report) now has improved word wrapping for multi-line messages and sniff codes
    - Thanks to Juliette Reinders Folmer for the patch
- The summary report now sorts files based on their directory location instead of just a basic string sort
    - Thanks to Juliette Reinders Folmer for the patch
- The source report now orders error codes by name when they have the same number of errors
    - Thanks to Juliette Reinders Folmer for the patch
- The junit report no longer generates validation errors with the Jenkins xUnit plugin
    - Thanks to Nikolay Geo for the patch
- Generic.Commenting.DocComment no longer generates the SpacingBeforeTags error if tags are the first content in the docblock
    - The sniff will still generate a MissingShort error if there is no short comment
    - This allows the MissingShort error to be suppressed in a ruleset to make short descriptions optional
- Generic.Functions.FunctionCallArgumentSpacing now properly fixes multi-line function calls with leading commas
    - Previously, newlines between function arguments would be removed
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.PHP.Syntax will now use PHP_BINARY instead of trying to discover the executable path
    - This ensures that the sniff will always syntax check files using the PHP version that PHPCS is running under
    - Setting the php_path config var will still override this value as normal
    - Thanks to Willem Stuursma-Ruwen for the patch
- PSR2.Namespaces.UseDeclaration now supports commas at the end of group use declarations
    - Also improves checking and fixing for use statements containing parse errors
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Arrays.ArrayDeclaration no longer removes the array opening brace while fixing
    - This could occur when the opening brace was on a new line and the first array key directly followed
    - This change also stops the KeyNotAligned error message being incorrectly reported in these cases
- Squiz.Arrays.ArrayDeclaration no longer tries to change multi-line arrays to single line when they contain comments
    - Fixes a conflict between this sniff and some indentation sniffs
- Squiz.Classes.ClassDeclaration no longer enforces spacing rules when a class is followed by a function
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.FunctionSpacing sniff
- The Squiz.Classes.ValidClassName.NotCamelCaps message now references PascalCase instead of CamelCase
    - The "CamelCase class name" metric produced by the sniff has been changed to "PascalCase class name"
    - This reflects the fact that the class name check is actually a Pascal Case check and not really Camel Case
    - Thanks to Tom H Anderson for the patch
- Squiz.Commenting.InlineComment no longer enforces spacing rules when an inline comment is followed by a docblock
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.FunctionSpacing sniff
- Squiz.WhiteSpace.OperatorSpacing no longer tries to fix operator spacing if the next content is a comment on a new line
    - Fixes a conflict between this sniff and the Squiz.Commenting.PostStatementComment sniff
    - Also stops PHPCS annotations from being moved to a different line, potentially changing their meaning
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.FunctionSpacing no longer checks spacing of functions at the top of an embedded PHP block
    - Fixes a conflict between this sniff and the Squiz.PHP.EmbeddedPHP sniff
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.MemberVarSpacing no longer checks spacing before member vars that come directly after methods
    - Fixes a conflict between this sniff and the Squiz.WhiteSpace.FunctionSpacing sniff
- Squiz.WhiteSpace.SuperfluousWhitespace now recognizes unicode whitespace at the start and end of a file
    - Thanks to Juliette Reinders Folmer for the patch

### Fixed
- Fixed bug #2029 : Squiz.Scope.MemberVarScope throws fatal error when a property is found in an interface
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2047 : PSR12.Classes.ClassInstantiation false positive when instantiating class from array index
- Fixed bug #2048 : GenericFormatting.MultipleStatementAlignment false positive when assigning values inside an array
- Fixed bug #2053 : PSR12.Classes.ClassInstantiation incorrectly fix when using member vars and some variable formats
- Fixed bug #2065 : Generic.ControlStructures.InlineControlStructure fixing fails when inline control structure contains closure
- Fixed bug #2072 : Squiz.Arrays.ArrayDeclaration throws NoComma error when array value is a shorthand IF statement
- Fixed bug #2082 : File with "defined() or define()" syntax triggers PSR1.Files.SideEffects.FoundWithSymbols
- Fixed bug #2095 : PSR2.Namespaces.NamespaceDeclaration does not handle namespaces defined over multiple lines


## [3.3.0] - 2018-06-07
### Deprecated
- The Squiz.WhiteSpace.LanguageConstructSpacing sniff has been deprecated and will be removed in version 4
    - The sniff has been moved to the Generic standard, with a new code of Generic.WhiteSpace.LanguageConstructSpacing
    - As soon as possible, replace all instances of the old sniff code with the new sniff code in your ruleset.xml files
        - The existing Squiz sniff will continue to work until version 4 has been released
    - The new Generic sniff now also checks many more language constructs to enforce additional spacing rules
        - Thanks to Mponos George for the contribution
- The current method for setting array properties in ruleset files has been deprecated and will be removed in version 4
    - Currently, setting an array value uses the string syntax "print=>echo,create_function=>null"
    - Now, individual array elements are specified using a new "element" tag with "key" and "value" attributes
        - For example, element key="print" value="echo"
    - Thanks to Michał Bundyra for the patch
- The T_ARRAY_HINT token has been deprecated and will be removed in version 4
    - The token was used to ensure array type hints were not tokenized as T_ARRAY, but no other type hints were given a special token
    - Array type hints now use the standard T_STRING token instead
    - Sniffs referencing this token type will continue to run without error until version 4, but will not find any T_ARRAY_HINT tokens
- The T_RETURN_TYPE token has been deprecated and will be removed in version 4
    - The token was used to ensure array/self/parent/callable return types were tokenized consistently
    - For namespaced return types, only the last part of the string (the class name) was tokenized as T_RETURN_TYPE
    - This was not consistent and so return types are now left using their original token types so they are not skipped by sniffs
        - The exception are array return types, which are tokenized as T_STRING instead of T_ARRAY, as they are for type hints
    - Sniffs referencing this token type will continue to run without error until version 4, but will not find any T_RETUTN_TYPE tokens
    - To get the return type of a function, use the File::getMethodProperties() method, which now contains a "return_type" array index
        - This contains the return type of the function or closer, or a blank string if not specified
        - If the return type is nullable, the return type will contain the leading ?
            - A nullable_return_type array index in the return value will also be set to true
        - If the return type contains namespace information, it will be cleaned of whitespace and comments
            - To access the original return value string, use the main tokens array

### Added
- This release contains an incomplete version of the PSR-12 coding standard
    - Errors found using this standard should be valid, but it will miss a lot of violations until it is complete
    - If you'd like to test and help, you can use the standard by running PHPCS with --standard=PSR12

### Changed
- Config values set using --runtime-set now override any config values set in rulesets or the CodeSniffer.conf file
- You can now apply include-pattern rules to individual message codes in a ruleset like you can with exclude-pattern rules
    - Previously, include-pattern rules only applied to entire sniffs
    - If a message code has both include and exclude patterns, the exclude patterns will be ignored
- Using PHPCS annotations to selectively re-enable sniffs is now more flexible
    - Previously, you could only re-enable a sniff/category/standard using the exact same code that was disabled
    - Now, you can disable a standard and only re-enable a specific category or sniff
    - Or, you can disable a specific sniff and have it re-enable when you re-enable the category or standard
- The value of array sniff properties can now be set using phpcs:set annotations
    - e.g., phpcs:set Standard.Category.SniffName property[] key=>value,key2=>value2
    - Thanks to Michał Bundyra for the patch
- PHPCS annotations now remain as T_PHPCS_* tokens instead of reverting to comment tokens when --ignore-annotations is used
    - This stops sniffs (especially commenting sniffs) from generating a large number of false errors when ignoring
    - Any custom sniffs that are using the T_PHPCS_* tokens to detect annotations may need to be changed to ignore them
        - Check $phpcsFile->config->annotations to see if annotations are enabled and ignore when false
- You can now use fully or partially qualified class names for custom reports instead of absolute file paths
    - To support this, you must specify an autoload file in your ruleset.xml file and use it to register an autoloader
    - Your autoloader will need to load your custom report class when requested
    - Thanks to Juliette Reinders Folmer for the patch
- The JSON report format now does escaping in error source codes as well as error messages
    - Thanks to Martin Vasel for the patch
- Invalid installed_paths values are now ignored instead of causing a fatal error
- Improved testability of custom rulesets by allowing the installed standards to be overridden
    - Thanks to Timo Schinkel for the patch
- The key used for caching PHPCS runs now includes all set config values
    - This fixes a problem where changing config values (e.g., via --runtime-set) used an incorrect cache file
- The "Function opening brace placement" metric has been separated into function and closure metrics in the info report
    - Closures are no longer included in the "Function opening brace placement" metric
    - A new "Closure opening brace placement" metric now shows information for closures
- Multi-line T_YIELD_FROM statements are now replicated properly for older PHP versions
- The PSR2 standard no longer produces 2 error messages when the AS keyword in a foreach loop is not lowercase
- Specifying a path to a non-existent dir when using the --report-[reportType]=/path/to/report CLI option no longer throws an exception
    - This now prints a readable error message, as it does when using --report-file
- The File::getMethodParamaters() method now includes a type_hint_token array index in the return value
    - Provides the position in the token stack of the first token in the type hint
- The File::getMethodProperties() method now includes a return_type_token array index in the return value
    - Provides the position in the token stack of the first token in the return type
- The File::getTokensAsString() method can now optionally return original (non tab-replaced) content
    - Thanks to Juliette Reinders Folmer for the patch
- Removed Squiz.PHP.DisallowObEndFlush from the Squiz standard
    - If you use this sniff and want to continue banning ob_end_flush(), use Generic.PHP.ForbiddenFunctions instead
    - You will need to set the forbiddenFunctions property in your ruleset.xml file
- Removed Squiz.PHP.ForbiddenFunctions from the Squiz standard
    - Replaced by using the forbiddenFunctions property of Generic.PHP.ForbiddenFunctions in the Squiz ruleset.xml
    - Functionality of the Squiz standard remains the same, but the error codes are now different
    - Previously, Squiz.PHP.ForbiddenFunctions.Found and Squiz.PHP.ForbiddenFunctions.FoundWithAlternative
    - Now, Generic.PHP.ForbiddenFunctions.Found and Generic.PHP.ForbiddenFunctions.FoundWithAlternative
- Added new Generic.PHP.LowerCaseType sniff
    - Ensures PHP types used for type hints, return types, and type casting are lowercase
    - Thanks to Juliette Reinders Folmer for the contribution
- Added new Generic.WhiteSpace.ArbitraryParenthesesSpacing sniff
    - Generates an error for whitespace inside parenthesis that don't belong to a function call/declaration or control structure
    - Generates a warning for any empty parenthesis found
    - Allows the required spacing to be set using the spacing sniff property (default is 0)
    - Allows newlines to be used by setting the ignoreNewlines sniff property (default is false)
    - Thanks to Juliette Reinders Folmer for the contribution
- Added new PSR12.Classes.ClassInstantiation sniff
    - Ensures parenthesis are used when instantiating a new class
- Added new PSR12.Keywords.ShortFormTypeKeywords sniff
    - Ensures the short form of PHP types is used when type casting
- Added new PSR12.Namespaces.CompundNamespaceDepth sniff
    - Ensures compound namespace use statements have a max depth of 2 levels
    - The max depth can be changed by setting the 'maxDepth' sniff property in a ruleset.xml file
- Added new PSR12.Operators.OperatorSpacing sniff
    - Ensures operators are preceded and followed by at least 1 space
- Improved core support for grouped property declarations
    - Also improves support in Squiz.WhiteSpace.ScopeKeywordSpacing and Squiz.WhiteSpace.MemberVarSpacing
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Commenting.DocComment now produces a NonParamGroup error when tags are mixed in with the @param tag group
    - It would previously throw either a NonParamGroup or ParamGroup error depending on the order of tags
    - This change allows the NonParamGroup error to be suppressed in a ruleset to allow the @param group to contain other tags
    - Thanks to Phil Davis for the patch
- Generic.Commenting.DocComment now continues checks param tags even if the doc comment short description is missing
    - This change allows the MissingShort error to be suppressed in a ruleset without all other errors being suppressed as well
    - Thanks to Phil Davis for the patch
- Generic.CodeAnalysis.AssignmentInCondition now reports a different error code for assignments found in WHILE conditions
    - The return value of a function call is often assigned in a WHILE condition, so this change makes it easier to exclude these cases
    - The new code for this error message is Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
    - The error code for all other cases remains as Generic.CodeAnalysis.AssignmentInCondition.Found
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Functions.OpeningFunctionBraceBsdAllman now longer leaves trailing whitespace when moving the opening brace during fixing
    - Also applies to fixes made by PEAR.Functions.FunctionDeclaration and Squiz.Functions.MultiLineFunctionDeclaration
- Generic.WhiteSpace.ScopeIndent now does a better job of fixing the indent of multi-line comments
- Generic.WhiteSpace.ScopeIndent now does a better job of fixing the indent of PHP open and close tags
- PEAR.Commenting.FunctionComment now report a different error code for param comment lines with too much padding
    - Previously, any lines of a param comment that don't start at the exact comment position got the same error code
    - Now, only comment lines with too little padding use ParamCommentAlignment as they are clearly mistakes
    - Comment lines with too much padding may be using precision alignment as now use ParamCommentAlignmentExceeded
    - This allows for excessive padding to be excluded from a ruleset while continuing to enforce a minimum padding
- PEAR.WhiteSpace.ObjectOperatorIndent now checks the indent of more chained operators
    - Previously, it only checked chains beginning with a variable
    - Now, it checks chains beginning with function calls, static class names, etc
- Squiz.Arrays.ArrayDeclaration now continues checking array formatting even if the key indent is not correct
    - Allows for using different array indent rules while still checking/fixing double arrow and value alignment
- Squiz.Commenting.BlockComment has improved support for tab-indented comments
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.BlockComment auto fixing no longer breaks when two block comments follow each other
    - Also stopped single-line block comments from being auto fixed when they are embedded in other code
    - Also fixed as issue found when PHPCS annotations were used inside a block comment
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.BlockComment.LastLineIndent is now able to be fixed with phpcbf
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.BlockComment now aligns star-prefixed lines under the opening tag while fixing, instead of indenting them
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.FunctionComment.IncorrectTypeHint message no longer contains cut-off suggested type hints
- Squiz.Commenting.InlineComment now uses a new error code for inline comments at the end of a function
    - Previously, all inline comments followed by a blank line threw a Squiz.Commenting.InlineComment.SpacingAfter error
    - Now, inline comments at the end of a function will instead throw Squiz.Commenting.InlineComment.SpacingAfterAtFunctionEnd
    - If you previously excluded SpacingAfter, add an exclusion for SpacingAfterAtFunctionEnd to your ruleset as well
    - If you previously only included SpacingAfter, consider including SpacingAfterAtFunctionEnd as well
    - The Squiz standard now excludes SpacingAfterAtFunctionEnd as the blank line is checked elsewhere
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.ControlStructures.ControlSignature now errors when a comment follows the closing brace of an earlier body
    - Applies to catch, finally, else, elseif, and do/while structures
    - The included PSR2 standard now enforces this rule
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Formatting.OperatorBracket.MissingBrackets message has been changed to remove the word "arithmetic"
    - The sniff checks more than just arithmetic operators, so the message is now clearer
- Sniffs.Operators.ComparisonOperatorUsage now detects more cases of implicit true comparisons
    - It could previously be confused by comparisons used as function arguments
- Squiz.PHP.CommentedOutCode now ignores simple @-style annotation comments so they are not flagged as commented out code
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.PHP.CommentedOutCode now ignores a greater number of short comments so they are not flagged as commented out code
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.PHP.DisallowComparisonAssignment no longer errors when using the null coalescing operator
    - Given this operator is used almost exclusively to assign values, it didn't make sense to generate an error
- Squiz.WhiteSpacing.FunctionSpacing now has a property to specify how many blank lines should be before the first class method
    - Only applies when a method is the first code block in a class (i.e., there are no member vars before it)
    - Override the 'spacingBeforeFirst' property in a ruleset.xml file to change
    - If not set, the sniff will use whatever value is set for the existing 'spacing' property
- Squiz.WhiteSpacing.FunctionSpacing now has a property to specify how many blank lines should be after the last class method
    - Only applies when a method is the last code block in a class (i.e., there are no member vars after it)
    - Override the 'spacingAfterLast' property in a ruleset.xml file to change
    - If not set, the sniff will use whatever value is set for the existing 'spacing' property

### Fixed
- Fixed bug #1863 : File::findEndOfStatement() not working when passed a scope opener
- Fixed bug #1876 : PSR2.Namespaces.UseDeclaration not giving error for use statements before the namespace declaration
    - Adds a new PSR2.Namespaces.UseDeclaration.UseBeforeNamespace error message
- Fixed bug #1881 : Generic.Arrays.ArrayIndent is indenting sub-arrays incorrectly when comma not used after the last value
- Fixed bug #1882 : Conditional with missing braces confused by indirect variables
- Fixed bug #1915 : JS tokenizer fails to tokenize regular expression proceeded by boolean not operator
- Fixed bug #1920 : Directory exclude pattern improperly excludes files with names that start the same
    - Thanks to Jeff Puckett for the patch
- Fixed bug #1922 : Equal sign alignment check broken when list syntax used before assignment operator
- Fixed bug #1925 : Generic.Formatting.MultipleStatementAlignment skipping assignments within closures
- Fixed bug #1931 : Generic opening brace placement sniffs do not correctly support function return types
- Fixed bug #1932 : Generic.ControlStructures.InlineControlStructure fixer moves new PHPCS annotations
- Fixed bug #1938 : Generic opening brace placement sniffs incorrectly move PHPCS annotations
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1939 : phpcs:set annotations do not cause the line they are on to be ignored
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1949 : Squiz.PHP.DisallowMultipleAssignments false positive when using namespaces with static assignments
- Fixed bug #1959 : SquizMultiLineFunctionDeclaration error when param has trailing comment
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1963 : Squiz.Scope.MemberVarScope does not work for multiline member declaration
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1971 : Short array list syntax not correctly tokenized if short array is the first content in a file
- Fixed bug #1979 : Tokenizer does not change heredoc to nowdoc token if the start tag contains spaces
- Fixed bug #1982 : Squiz.Arrays.ArrayDeclaration fixer sometimes puts a comma in front of the last array value
- Fixed bug #1993 : PSR1/PSR2 not reporting or fixing short open tags
- Fixed bug #1996 : Custom report paths don't work on case-sensitive filesystems
- Fixed bug #2006 : Squiz.Functions.FunctionDeclarationArgumentSpacing fixer removes comment between parens when no args
    - The SpacingAfterOpenHint error message has been removed
        - It is replaced by the the existing SpacingAfterOpen message
    - The error message format for the SpacingAfterOpen and SpacingBeforeClose messages has been changed
        - These used to contain 3 pieces of data, but now only contain 2
    - If you have customised the error messages of this sniff, please review your ruleset after upgrading
- Fixed bug #2018 : Generic.Formatting.MultipleStatementAlignment does see PHP close tag as end of statement block
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #2027 : PEAR.NamingConventions.ValidFunctionName error when function name includes double underscore
    - Thanks to Juliette Reinders Folmer for the patch


## [3.2.3] - 2018-02-21
### Changed
- The new phpcs: comment syntax can now be prefixed with an at symbol ( @phpcs: )
    - This restores the behaviour of the previous syntax where these comments are ignored by doc generators
- The current PHP version ID is now used to generate cache files
    - This ensures that only cache files generated by the current PHP version are selected
    - This change fixes caching issues when using sniffs that produce errors based on the current PHP version
- A new Tokens::$phpcsCommentTokens array is now available for sniff developers to detect phpcs: comment syntax
    - Thanks to Juliette Reinders Folmer for the patch
- The PEAR.Commenting.FunctionComment.Missing error message now includes the name of the function
    - Thanks to Yorman Arias for the patch
- The PEAR.Commenting.ClassComment.Missing and Squiz.Commenting.ClassComment.Missing error messages now include the name of the class
    - Thanks to Yorman Arias for the patch
- PEAR.Functions.FunctionCallSignature now only forces alignment at a specific tab stop while fixing
    - It was enforcing this during checking, but this meant invalid errors if the OpeningIndent message was being muted
    - This fixes incorrect errors when using the PSR2 standard with some code blocks
- Generic.Files.LineLength now ignores lines that only contain phpcs: annotation comments
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Formatting.MultipleStatementAlignment now skips over arrays containing comments
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.PHP.Syntax now forces display_errors to ON when linting
    - Thanks to Raúl Arellano for the patch
- PSR2.Namespaces.UseDeclaration has improved syntax error handling and closure detection
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.PHP.CommentedOutCode now has improved comment block detection for improved accuracy
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.PHP.NonExecutableCode could fatal error while fixing file with syntax error
- Squiz.PHP.NonExecutableCode now detects unreachable code after a goto statement
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.LanguageConstructSpacing has improved syntax error handling while fixing
    - Thanks to Juliette Reinders Folmer for the patch
- Improved phpcs: annotation syntax handling for a number of sniffs
    - Thanks to Juliette Reinders Folmer for the patch
- Improved auto-fixing of files with incomplete comment blocks for various commenting sniffs
    - Thanks to Juliette Reinders Folmer for the patch

### Fixed
- Fixed test suite compatibility with PHPUnit 7
- Fixed bug #1793 : PSR2 forcing exact indent for function call opening statements
- Fixed bug #1803 : Squiz.WhiteSpace.ScopeKeywordSpacing removes member var name while fixing if no space after scope keyword
- Fixed bug #1817 : Blank line not enforced after control structure if comment on same line as closing brace
- Fixed bug #1827 : A phpcs:enable comment is not tokenized correctly if it is outside a phpcs:disable block
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1828 : Squiz.WhiteSpace.SuperfluousWhiteSpace ignoreBlankLines property ignores whitespace after single line comments
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1840 : When a comment has too many asterisks, phpcbf gives FAILED TO FIX error
- Fixed bug #1867 : Cant use phpcs:ignore where the next line is HTML
- Fixed bug #1870 : Invalid warning in multiple assignments alignment with closure or anon class
- Fixed bug #1890 : Incorrect Squiz.WhiteSpace.ControlStructureSpacing.NoLineAfterClose error between catch and finally statements
- Fixed bug #1891 : Comment on last USE statement causes false positive for PSR2.Namespaces.UseDeclaration.SpaceAfterLastUse
    - Thanks to Matt Coleman, Daniel Hensby, and Juliette Reinders Folmer for the patch
- Fixed bug #1901 : Fixed PHPCS annotations in multi-line tab-indented comments + not ignoring whole line for phpcs:set
    - Thanks to Juliette Reinders Folmer for the patch


## [3.2.2] - 2017-12-20
### Changed
- Disabled STDIN detection on Windows
    - This fixes a problem with IDE plugins (e.g., PHPStorm) hanging on Windows


## [3.2.1] - 2017-12-18
### Changed
- Empty diffs are no longer followed by a newline character (request #1781)
- Generic.Functions.OpeningFunctionBraceKernighanRitchie no longer complains when the open brace is followed by a close tag
    - This makes the sniff more useful when used in templates
    - Thanks to Joseph Zidell for the patch

### Fixed
- Fixed problems with some scripts and plugins waiting for STDIN
    - This was a notable problem with IDE plugins (e.g., PHPStorm) and build systems
- Fixed bug #1782 : Incorrect detection of operator in ternary + anonymous function


## [3.2.0] - 2017-12-13
### Deprecated
- This release deprecates the @codingStandards comment syntax used for sending commands to PHP_CodeSniffer
    - The existing syntax will continue to work in all version 3 releases, but will be removed in version 4
    - The comment formats have been replaced by a shorter syntax:
        - @codingStandardsIgnoreFile becomes phpcs:ignoreFile
        - @codingStandardsIgnoreStart becomes phpcs:disable
        - @codingStandardsIgnoreEnd becomes phpcs:enable
        - @codingStandardsIgnoreLine becomes phpcs:ignore
        - @codingStandardsChangeSetting becomes phpcs:set
    - The new syntax allows for additional developer comments to be added after a -- separator
        - This is useful for describing why a code block is being ignored, or why a setting is being changed
        - E.g., phpcs:disable -- This code block must be left as-is.
    - Comments using the new syntax are assigned new comment token types to allow them to be detected:
        - phpcs:ignoreFile has the token T_PHPCS_IGNORE_FILE
        - phpcs:disable has the token T_PHPCS_DISABLE
        - phpcs:enable has the token T_PHPCS_ENABLE
        - phpcs:ignore has the token T_PHPCS_IGNORE
        - phpcs:set has the token T_PHPCS_SET

### Changed
- The phpcs:disable and phpcs:ignore comments can now selectively ignore specific sniffs (request #604)
    - E.g., phpcs:disable Generic.Commenting.Todo.Found for a specific message
    - E.g., phpcs:disable Generic.Commenting.Todo for a whole sniff
    - E.g., phpcs:disable Generic.Commenting for a whole category of sniffs
    - E.g., phpcs:disable Generic for a whole standard
    - Multiple sniff codes can be specified by comma separating them
        - E.g., phpcs:disable Generic.Commenting.Todo,PSR1.Files
- @codingStandardsIgnoreLine comments now only ignore the following line if they are on a line by themselves
    - If they are at the end of an existing line, they will only ignore the line they are on
    - Stops some lines from accidentally being ignored
    - Same rule applies for the new phpcs:ignore comment syntax
- PSR1.Files.SideEffects now respects the new phpcs:disable comment syntax
    - The sniff will no longer check any code that is between phpcs:disable and phpcs:enable comments
    - The sniff does not support phpcs:ignore; you must wrap code structures with disable/enable comments
    - Previously, there was no way to have this sniff ignore parts of a file
- Fixed a problem where PHPCS would sometimes hang waiting for STDIN, or read incomplete versions of large files
    - Thanks to Arne Jørgensen for the patch
- Array properties specified in ruleset files now have their keys and values trimmed
    - This saves having to do this in individual sniffs and stops errors introduced by whitespace in rulesets
    - Thanks to Juliette Reinders Folmer for the patch
- Added phpcs.xsd to allow validation of ruleset XML files
    - Thanks to Renaat De Muynck for the contribution
- File paths specified using --stdin-path can now point to fake file locations (request #1488)
    - Previously, STDIN files using fake file paths were excluded from checking
- Setting an empty basepath (--basepath=) on the CLI will now clear a basepath set directly in a ruleset
    - Thanks to Xaver Loppenstedt for the patch
- Ignore patterns are now checked on symlink target paths instead of symlink source paths
    - Restores previous behaviour of this feature
- Metrics were being double counted when multiple sniffs were recording the same metric
- Added support for bash process substitution
    - Thanks to Scott Dutton for the contribution
- Files included in the cache file code hash are now sorted to aid in cache file reuse across servers
- Windows BAT files can now be used outside a PEAR install
    - You must have the path to PHP set in your PATH environment variable
    - Thanks to Joris Debonnet for the patch
- The JS unsigned right shift assignment operator is now properly classified as an assignment operator
    - Thanks to Juliette Reinders Folmer for the patch
- The AbstractVariableSniff abstract sniff now supports anonymous classes and nested functions
    - Also fixes an issue with Squiz.Scope.MemberVarScope where member vars of anonymous classes were not being checked
- Added AbstractArraySniff to make it easier to create sniffs that check array formatting
    - Allows for checking of single and multi line arrays easily
    - Provides a parsed structure of the array including positions of keys, values, and double arrows
- Added Generic.Arrays.ArrayIndent to enforce a single tab stop indent for array keys in multi-line arrays
    - Also ensures the close brace is on a new line and indented to the same level as the original statement
    - Allows for the indent size to be set using an "indent" property of the sniff
- Added Generic.PHP.DiscourageGoto to warn about the use of the GOTO language construct
    - Thanks to Juliette Reinders Folmer for the contribution
- Generic.Debug.ClosureLinter was not running the gjslint command
    - Thanks to Michał Bundyra for the patch
- Generic.WhiteSpace.DisallowSpaceIndent now fixes space indents in multi-line block comments
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.WhiteSpace.DisallowSpaceIndent now fixes mixed space/tab indents more accurately
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.WhiteSpace.DisallowTabIndent now fixes tab indents in multi-line block comments
    - Thanks to Juliette Reinders Folmer for the patch
- PEAR.Functions.FunctionDeclaration no longer errors when a function declaration is the first content in a JS file
    - Thanks to Juliette Reinders Folmer for the patch
- PEAR.Functions.FunctionCallSignature now requires the function name to be indented to an exact tab stop
    - If the function name is not the start of the statement, the opening statement must be indented correctly instead
    - Added a new fixable error code PEAR.Functions.FunctionCallSignature.OpeningIndent for this error
- Squiz.Functions.FunctionDeclarationArgumentSpacing is no longer confused about comments in function declarations
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.PHP.NonExecutableCode error messages now indicate which line the code block ending is on
    - Makes it easier to identify where the code block exited or returned
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.FunctionComment now supports nullable type hints
- Squiz.Commenting.FunctionCommentThrowTag no longer assigns throw tags inside anon classes to the enclosing function
- Squiz.WhiteSpace.SemicolonSpacing now ignores semicolons used for empty statements inside FOR conditions
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.ControlStructures.ControlSignature now allows configuring the number of spaces before the colon in alternative syntax
    - Override the 'requiredSpacesBeforeColon' setting in a ruleset.xml file to change
    - Default remains at 1
    - Thanks to Nikola Kovacs for the patch
- The Squiz standard now ensures array keys are indented 4 spaces from the main statement
    - Previously, this standard aligned keys 1 space from the start of the array keyword
- The Squiz standard now ensures array end braces are aligned with the main statement
    - Previously, this standard aligned the close brace with the start of the array keyword
- The standard for PHP_CodeSniffer itself now enforces short array syntax
- The standard for PHP_CodeSniffer itself now uses the Generic.Arrays/ArrayIndent sniff rules
- Improved fixer conflicts and syntax error handling for a number of sniffs
    - Thanks to Juliette Reinders Folmer for the patch

### Fixed
- Fixed bug #1462 : Error processing cyrillic strings in Tokenizer
- Fixed bug #1573 : Squiz.WhiteSpace.LanguageConstructSpacing does not properly check for tabs and newlines
    - Thanks to Michał Bundyra for the patch
- Fixed bug #1590 : InlineControlStructure CBF issue while adding braces to an if thats returning a nested function
- Fixed bug #1718 : Unclosed strings at EOF sometimes tokenized as T_WHITESPACE by the JS tokenizer
- Fixed bug #1731 : Directory exclusions do not work as expected when a single file name is passed to phpcs
- Fixed bug #1737 : Squiz.CSS.EmptyStyleDefinition sees comment as style definition and fails to report error
- Fixed bug #1746 : Very large reports can sometimes become garbled when using the parallel option
- Fixed bug #1747 : Squiz.Scope.StaticThisUsage incorrectly looking inside closures
- Fixed bug #1757 : Unknown type hint "object" in Squiz.Commenting.FunctionComment
- Fixed bug #1758 : PHPCS gets stuck creating file list when processing circular symlinks
- Fixed bug #1761 : Generic.WhiteSpace.ScopeIndent error on multi-line function call with static closure argument
- Fixed bug #1762 : Generic.WhiteSpace.Disallow[Space/Tab]Indent not inspecting content before open tag
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1769 : Custom "define" function triggers a warning about declaring new symbols
- Fixed bug #1776 : Squiz.Scope.StaticThisUsage incorrectly looking inside anon classes
- Fixed bug #1777 : Generic.WhiteSpace.ScopeIndent incorrect indent errors when self called function proceeded by comment


## [3.1.1] - 2017-10-17
### Changed
- Restored preference of non-dist files over dist files for phpcs.xml and phpcs.xml.dist
    - The order that the files are searched is now: .phpcs.xml, phpcs.xml, .phpcs.xml.dist, phpcs.xml.dist
    - Thanks to Juliette Reinders Folmer for the patch
- Progress output now correctly shows skipped files
- Progress output now shows 100% when the file list has finished processing (request #1697)
- Stopped some IDEs complaining about testing class aliases
    - Thanks to Vytautas Stankus for the patch
- Squiz.Commenting.InlineComment incorrectly identified comment blocks in some cases, muting some errors
    - Thanks to Juliette Reinders Folmer for the patch

### Fixed
- Fixed bug #1512 : PEAR.Functions.FunctionCallSignature enforces spaces when no arguments if required spaces is not 0
- Fixed bug #1522 : Squiz Arrays.ArrayDeclaration and Strings.ConcatenationSpacing fixers causing parse errors with here/  nowdocs
- Fixed bug #1570 : Squiz.Arrays.ArrayDeclaration fixer removes comments between array keyword and open parentheses
- Fixed bug #1604 : File::isReference has problems with some bitwise operators and class property references
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1645 : Squiz.Commenting.InlineComment will fail to fix comments at the end of the file
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1656 : Using the --sniffs argument has a problem with case sensitivity
- Fixed bug #1657 : Uninitialized string offset: 0 when sniffing CSS
- Fixed bug #1669 : Temporary expression proceeded by curly brace is detected as function call
- Fixed bug #1681 : Huge arrays are super slow to scan with Squiz.Arrays.ArrayDeclaration sniff
- Fixed bug #1694 : Squiz.Arrays.ArrayBracketSpacing is removing some comments during fixing
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1702 : Generic.WhiteSpaceDisallowSpaceIndent fixer bug when line only contains superfluous whitespace


## [3.1.0] - 2017-09-20
### Changed
- This release includes a change to support newer versions of PHPUnit (versions 4, 5, and 6 are now supported)
    - The custom PHP_CodeSniffer test runner now requires a bootstrap file
    - Developers with custom standards using the PHP_CodeSniffer test runner will need to do one of the following:
      - run your unit tests from the PHP_CodeSniffer root dir so the bootstrap file is included
      - specify the PHP_CodeSniffer bootstrap file on the command line: phpunit --bootstrap=/path/to/phpcs/tests/bootstrap.php
      - require the PHP_CodeSniffer bootstrap file from your own bootstrap file
    - If you don't run PHP_CodeSniffer unit tests, this change will not affect you
    - Thanks to Juliette Reinders Folmer for the patch
- A phpcs.xml or phpcs.xml.dist file now takes precedence over the default_standard config setting
    - Thanks to Björn Fischer for the patch
- Both phpcs.xml and phpcs.xml.dist files can now be prefixed with a dot (request #1566)
    - The order that the files are searched is: .phpcs.xml, .phpcs.xml.dist, phpcs.xml, phpcs.xml.dist
- The autoloader will now search for files during unit tests runs from the same locations as during normal phpcs runs
    - Allows for easier unit testing of custom standards that use helper classes or custom namespaces
- Include patterns for sniffs now use OR logic instead of AND logic
    - Previously, a file had to be in each of the include patterns to be processed by a sniff
    - Now, a file has to only be in at least one of the patterns
    - This change reflects the original intention of the feature
- PHPCS will now follow symlinks under the list of checked directories
    - This previously only worked if you specified the path to a symlink on the command line
- Output from --config-show, --config-set, and --config-delete now includes the path to the loaded config file
- PHPCS now cleanly exits if its config file is not readable
    - Previously, a combination of PHP notices and PHPCS errors would be generated
- Comment tokens that start with /** are now always tokenized as docblocks
    - Thanks to Michał Bundyra for the patch
- The PHP-supplied T_YIELD and T_YIELD_FROM token have been replicated for older PHP versions
    - Thanks to Michał Bundyra for the patch
- Added new Generic.CodeAnalysis.AssignmentInCondition sniff to warn about variable assignments inside conditions
    - Thanks to Juliette Reinders Folmer for the contribution
- Added Generic.Files.OneObjectStructurePerFile sniff to ensure there is a single class/interface/trait per file
    - Thanks to Mponos George for the contribution
- Function call sniffs now check variable function names and self/static object creation
    - Specific sniffs are Generic.Functions.FunctionCallArgumentSpacing, PEAR.Functions.FunctionCallSignature, and   PSR2.Methods.FunctionCallSignature
    - Thanks to Michał Bundyra for the patch
- Generic.Files.LineLength can now be configured to ignore all comment lines, no matter their length
    - Set the ignoreComments property to TRUE (default is FALSE) in your ruleset.xml file to enable this
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.PHP.LowerCaseKeyword now checks self, parent, yield, yield from, and closure (function) keywords
    - Thanks to Michał Bundyra for the patch
- PEAR.Functions.FunctionDeclaration now removes a blank line if it creates one by moving the curly brace during fixing
- Squiz.Commenting.FunctionCommentThrowTag now supports PHP 7.1 multi catch exceptions
- Squiz.Formatting.OperatorBracket no longer throws errors for PHP 7.1 multi catch exceptions
- Squiz.Commenting.LongConditionClosingComment now supports finally statements
- Squiz.Formatting.OperatorBracket now correctly fixes pipe separated flags
- Squiz.Formatting.OperatorBracket now correctly fixes statements containing short array syntax
- Squiz.PHP.EmbeddedPhp now properly fixes cases where the only content in an embedded PHP block is a comment
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.ControlStructureSpacing now ignores comments when checking blank lines at the top of control structures
- Squiz.WhiteSpace.ObjectOperatorSpacing now detects and fixes spaces around double colons
    - Thanks to Julius Šmatavičius for the patch
- Squiz.WhiteSpace.MemberVarSpacing can now be configured to check any number of blank lines between member vars
    - Set the spacing property (default is 1) in your ruleset.xml file to set the spacing
- Squiz.WhiteSpace.MemberVarSpacing can now be configured to check a different number of blank lines before the first member var
    - Set the spacingBeforeFirst property (default is 1) in your ruleset.xml file to set the spacing
- Added a new PHP_CodeSniffer\Util\Tokens::$ooScopeTokens static member var for quickly checking object scope
    - Includes T_CLASS, T_ANON_CLASS, T_INTERFACE, and T_TRAIT
    - Thanks to Juliette Reinders Folmer for the patch
- PHP_CodeSniffer\Files\File::findExtendedClassName() now supports extended interfaces
    - Thanks to Martin Hujer for the patch

### Fixed
- Fixed bug #1550 : Squiz.Commenting.FunctionComment false positive when function contains closure
- Fixed bug #1577 : Generic.InlineControlStructureSniff breaks with a comment between body and condition in do while loops
- Fixed bug #1581 : Sniffs not loaded when one-standard directories are being registered in installed_paths
- Fixed bug #1591 : Autoloader failing to load arbitrary files when installed_paths only set via a custom ruleset
- Fixed bug #1605 : Squiz.WhiteSpace.OperatorSpacing false positive on unary minus after comment
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1615 : Uncaught RuntimeException when phpcbf fails to fix files
- Fixed bug #1637 : Generic.WhiteSpaceScopeIndent closure argument indenting incorrect with multi-line strings
- Fixed bug #1638 : Squiz.WhiteSpace.ScopeClosingBrace closure argument indenting incorrect with multi-line strings
- Fixed bug #1640 : Squiz.Strings.DoubleQuoteUsage replaces tabs with spaces when fixing
    - Thanks to Juliette Reinders Folmer for the patch


## [3.0.2] - 2017-07-18
### Changed
- The code report now gracefully handles tokenizer exceptions
- The phpcs and phpcbf scripts and now the only places that exit() in the code
    - This allows for easier usage of core PHPCS functions from external scripts
    - If you are calling Runner::runPHPCS() or Runner::runPHPCBF() directly, you will get back the full range of exit codes
    - If not, catch the new DeepExitException to get the error message ($e->getMessage()) and exit code ($e->getCode());
- NOWDOC tokens are now considered conditions, just as HEREDOC tokens are
    - This makes it easier to find the start and end of a NOWDOC from any token within it
    - Thanks to Michał Bundyra for the patch
- Custom autoloaders are now only included once in case multiple standards are using the same one
    - Thanks to Juliette Reinders Folmer for the patch
- Improved tokenizing of fallthrough CASE and DEFAULT statements that share a closing statement and use curly braces
- Improved the error message when Squiz.ControlStructures.ControlSignature detects a newline after the closing parenthesis

### Fixed
- Fixed a problem where the source report was not printing the correct number of errors found
- Fixed a problem where the --cache=/path/to/cachefile CLI argument was not working
- Fixed bug #1465 : Generic.WhiteSpace.ScopeIndent reports incorrect errors when indenting double arrows in short arrays
- Fixed bug #1478 : Indentation in fallthrough CASE that contains a closure
- Fixed bug #1497 : Fatal error if composer prepend-autoloader is set to false
    - Thanks to Kunal Mehta for the patch
- Fixed bug #1503 : Alternative control structure syntax not always recognized as scoped
- Fixed bug #1523 : Fatal error when using the --suffix argument
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1526 : Use of basepath setting can stop PHPCBF being able to write fixed files
- Fixed bug #1530 : Generic.WhiteSpace.ScopeIndent can increase indent too much for lines within code blocks
- Fixed bug #1547 : Wrong token type for backslash in use function
    - Thanks to Michał Bundyra for the patch
- Fixed bug #1549 : Squiz.PHP.EmbeddedPhp fixer conflict with // comment before PHP close tag
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1560 : Squiz.Commenting.FunctionComment fatal error when fixing additional param comment lines that have no indent


## [3.0.1] - 2017-06-14
### Security
- This release contains a fix for a security advisory related to the improper handling of a shell command
    - A properly crafted filename would allow for arbitrary code execution when using the --filter=gitmodified command line option
    - All version 3 users are encouraged to upgrade to this version, especially if you are checking 3rd-party code
        - e.g., you run PHPCS over libraries that you did not write
        - e.g., you provide a web service that runs PHPCS over user-uploaded files or 3rd-party repositories
        - e.g., you allow external tool paths to be set by user-defined values
    - If you are unable to upgrade but you check 3rd-party code, ensure you are not using the Git modified filter
    - This advisory does not affect PHP_CodeSniffer version 2.
    - Thanks to Sergei Morozov for the report and patch

### Changed
- Arguments on the command line now override or merge with those specified in a ruleset.xml file in all cases
- PHPCS now stops looking for a phpcs.xml file as soon as one is found, favoring the closest one to the current dir
- Added missing help text for the --stdin-path CLI option to --help
- Re-added missing help text for the --file-list and --bootstrap CLI options to --help
- Runner::runPHPCS() and Runner::runPHPCBF() now return an exit code instead of exiting directly (request #1484)
- The Squiz standard now enforces short array syntax by default
- The autoloader is now working correctly with classes created with class_alias()
- The autoloader will now search for files inside all directories in the installed_paths config var
    - This allows autoloading of files inside included custom coding standards without manually requiring them
- You can now specify a namespace for a custom coding standard, used by the autoloader to load non-sniff helper files
    - Also used by the autoloader to help other standards directly include sniffs for your standard
    - Set the value to the namespace prefix you are using for sniff files (everything up to \Sniffs\)
    - e.g., if your namespace format is MyProject\CS\Standard\Sniffs\Category set the namespace to MyProject\CS\Standard
    - If omitted, the namespace is assumed to be the same as the directory name containing the ruleset.xml file
    - The namespace is set in the ruleset tag of the ruleset.xml file
    - e.g., ruleset name="My Coding Standard" namespace="MyProject\CS\Standard"
- Rulesets can now specify custom autoloaders using the new autoload tag
    - Autoloaders are included while the ruleset is being processed and before any custom sniffs are included
    - Allows for very custom autoloading of helper classes well before the boostrap files are included
- The PEAR standard now includes Squiz.Commenting.DocCommentAlignment
    - It previously broke comments onto multiple lines, but didn't align them

### Fixed
- Fixed a problem where excluding a message from a custom standard's own sniff would exclude the whole sniff
    - This caused some PSR2 errors to be under-reported
- Fixed bug #1442 : T_NULLABLE detection not working for nullable parameters and return type hints in some cases
- Fixed bug #1447 : Running the unit tests with a phpunit config file breaks the test suite
    - Unknown arguments were not being handled correctly, but are now stored in $config->unknown
- Fixed bug #1449 : Generic.Classes.OpeningBraceSameLine doesn't detect comment before opening brace
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1450 : Coding standard located under an installed_path with the same directory name throws an error
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1451 : Sniff exclusions/restrictions dont work with custom sniffs unless they use the PHP_CodeSniffer NS
- Fixed bug #1454 : Squiz.WhiteSpace.OperatorSpacing is not checking spacing on either side of a short ternary operator
    - Thanks to Mponos George for the patch
- Fixed bug #1495 : Setting an invalid installed path breaks all commands
- Fixed bug #1496 : Squiz.Strings.DoubleQuoteUsage not unescaping dollar sign when fixing
    - Thanks to Michał Bundyra for the patch
- Fixed bug #1501 : Interactive mode is broken
- Fixed bug #1504 : PSR2.Namespaces.UseDeclaration hangs fixing use statement with no trailing code


## [2.9.1] - 2017-05-22
### Fixed
- Fixed bug #1442 : T_NULLABLE detection not working for nullable parameters and return type hints in some cases
- Fixed bug #1448 : Generic.Classes.OpeningBraceSameLine doesn't detect comment before opening brace
    - Thanks to Juliette Reinders Folmer for the patch


## [3.0.0] - 2017-05-04
### Changed
- Added an --ignore-annotations command line argument to ignore all @codingStandards annotations in code comments (request #811)
- This allows you to force errors to be shown that would otherwise be ignored by code comments
    - Also stop files being able to change sniff properties mid way through processing
- An error is now reported if no sniffs were registered to be run (request #1129)
- The autoloader will now search for files inside the directory of any loaded coding standard
    - This allows autoloading of any file inside a custom coding standard without manually requiring them
    - Ensure your namespace begins with your coding standard's directory name and follows PSR-4
    - e.g., StandardName\Sniffs\CategoryName\AbstractHelper or StandardName\Helpers\StringSniffHelper
- Fixed an error where STDIN was sometimes not checked when using the --parallel CLI option
- The is_closure index has been removed from the return value of File::getMethodProperties()
    - This value was always false because T_FUNCTION tokens are never closures
    - Closures have a token type of T_CLOSURE
- The File::isAnonymousFunction() method has been removed
    - This function always returned false because it only accepted T_FUNCTION tokens, which are never closures
    - Closures have a token type of T_CLOSURE
- Includes all changes from the 2.9.0 release

### Fixed
- Fixed bug #834 : PSR2.ControlStructures.SwitchDeclaration does not handle if branches with returns
    - Thanks to Fabian Wiget for the patch


## [3.0.0RC4] - 2017-03-02
### Security
- This release contains a fix for a security advisory related to the improper handling of shell commands
    - Uses of shell_exec() and exec() were not escaping filenames and configuration settings in most cases
    - A properly crafted filename or configuration option would allow for arbitrary code execution when using some features
    - All users are encouraged to upgrade to this version, especially if you are checking 3rd-party code
        - e.g., you run PHPCS over libraries that you did not write
        - e.g., you provide a web service that runs PHPCS over user-uploaded files or 3rd-party repositories
        - e.g., you allow external tool paths to be set by user-defined values
    - If you are unable to upgrade but you check 3rd-party code, ensure you are not using the following features:
        - The diff report
        - The notify-send report
        - The Generic.PHP.Syntax sniff
        - The Generic.Debug.CSSLint sniff
        - The Generic.Debug.ClosureLinter sniff
        - The Generic.Debug.JSHint sniff
        - The Squiz.Debug.JSLint sniff
        - The Squiz.Debug.JavaScriptLint sniff
        - The Zend.Debug.CodeAnalyzer sniff
    - Thanks to Klaus Purer for the report

### Changed
- The indent property of PEAR.Classes.ClassDeclaration has been removed
    - Instead of calculating the indent of the brace, it just ensures the brace is aligned with the class keyword
    - Other sniffs can be used to ensure the class itself is indented correctly
- Invalid exclude rules inside a ruleset.xml file are now ignored instead of potentially causing out of memory errors
    - Using the -vv command line argument now also shows the invalid exclude rule as XML
- Includes all changes from the 2.8.1 release

### Fixed
- Fixed bug #1333 : The new autoloader breaks some frameworks with custom autoloaders
- Fixed bug #1334 : Undefined offset when explaining standard with custom sniffs


## [3.0.0RC3] - 2017-02-02
### Changed
- Added support for ES6 class declarations
    - Previously, these class were tokenized as JS objects but are now tokenized as normal T_CLASS structures
- Added support for ES6 method declarations, where the "function" keyword is not used
    - Previously, these methods were tokenized as JS objects (fixes bug #1251)
    - The name of the ES6 method is now assigned the T_FUNCTION keyword and treated like a normal function
    - Custom sniffs that support JS and listen for T_FUNCTION tokens can't assume the token represents the word   "function"
    - Check the contents of the token first, or use $phpcsFile->getDeclarationName($stackPtr) if you just want its name
    - There is no change for custom sniffs that only check PHP code
- PHPCBF exit codes have been changed so they are now more useful (request #1270)
    - Exit code 0 is now used to indicate that no fixable errors were found, and so nothing was fixed
    - Exit code 1 is now used to indicate that all fixable errors were fixed correctly
    - Exit code 2 is now used to indicate that PHPCBF failed to fix some of the fixable errors it found
    - Exit code 3 is now used for general script execution errors
- Added PEAR.Commenting.FileComment.ParamCommentAlignment to check alignment of multi-line param comments
- Includes all changes from the 2.8.0 release

### Fixed
- Fixed an issue where excluding a file using a @codingStandardsIgnoreFile comment would produce errors
    - For PHPCS, it would show empty files being processed
    - For PHPCBF, it would produce a PHP error
- Fixed bug #1233 : Can't set config data inside ruleset.xml file
- Fixed bug #1241 : CodeSniffer.conf not working with 3.x PHAR file


## [3.0.0RC2] - 2016-11-30
### Changed
- Made the Runner class easier to use with wrapper scripts
- Full usage information is no longer printed when a usage error is encountered (request #1186)
    - Makes it a lot easier to find and read the error message that was printed
- Includes all changes from the 2.7.1 release

### Fixed
- Fixed an undefined var name error that could be produced while running PHPCBF
- Fixed bug #1167 : 3.0.0RC1 PHAR does not work with PEAR standard
- Fixed bug #1208 : Excluding files doesn't work when using STDIN with a filename specified


## [3.0.0RC1] - 2016-09-02
### Changed
- Progress output now shows E and W in green when a file has fixable errors or warnings
    - Only supported if colors are enabled
- PHPCBF no longer produces verbose output by default (request #699)
    - Use the -v command line argument to show verbose fixing output
    - Use the -q command line argument to disable verbose information if enabled by default
- PHPBF now prints a summary report after fixing files
    - Report shows files that were fixed, how many errors were fixed, and how many remain
- PHPCBF now supports the -p command line argument to print progress information
    - Prints a green F for files where fixes occurred
    - Prints a red E for files that could not be fixed due to an error
    - Use the -q command line argument to disable progress information if enabled by default
- Running unit tests using --verbose no longer throws errors
- Includes all changes from the 2.7.0 release

### Fixed
- Fixed shell error appearing on some systems when trying to find executable paths


## [3.0.0a1] - 2016-07-20
### Changed
- Min PHP version increased from 5.1.2 to 5.4.0
- Added optional caching of results between runs (request #530)
    - Enable the cache by using the --cache command line argument
    - If you want the cache file written somewhere specific, use --cache=/path/to/cacheFile
    - Use the command "phpcs --config-set cache true" to turn caching on by default
    - Use the --no-cache command line argument to disable caching if it is being turned on automatically
- Add support for checking file in parallel (request #421)
    - Tell PHPCS how many files to check at once using the --parallel command line argument
    - To check 100 files at once, using --parallel=100
    - To disable parallel checking if it is being turned on automatically, use --parallel=1
    - Requires PHP to be compiled with the PCNTL package
- The default encoding has been changed from iso-8859-1 to utf-8 (request #760)
    - The --encoding command line argument still works, but you no longer have to set it to process files as utf-8
    - If encoding is being set to utf-8 in a ruleset or on the CLI, it can be safely removed
    - If the iconv PHP extension is not installed, standard non-multibyte aware functions will be used
- Added a new "code" report type to show a code snippet for each error (request #419)
    - The line containing the error is printed, along with 2 lines above and below it to show context
    - The location of the errors is underlined in the code snippet if you also use --colors
    - Use --report=code to generate this report
- Added support for custom filtering of the file list
    - Developers can write their own filter classes to perform custom filtering of the list before the run starts
    - Use the command line arg --filter=/path/to/filter.php to specify a filter to use
    - Extend \PHP_CodeSniffer\Filters\Filter to also support the core PHPCS extension and path filtering
    - Extend \PHP_CodeSniffer\Filters\ExactMatch to get the core filtering and the ability to use blacklists and whitelists
    - The included \PHP_CodeSniffer\Filters\GitModified filter is a good example of an ExactMatch filter
- Added support for only checking files that have been locally modified or added in a git repo
    - Use --filter=gitmodified to check these files
    - You still need to give PHPCS a list of files or directories in which to check
- Added automatic discovery of executable paths (request #571)
    - Thanks to Sergey Morozov for the patch
- You must now pass "-" on the command line to have PHPCS wait for STDIN
    - E.g., phpcs --standard=PSR2 -
    - You can still pipe content via STDIN as normal as PHPCS will see this and process it
    - But without the "-", PHPCS will throw an error if no content or files are passed to it
- All PHP errors generated by sniffs are caught, re-thrown as exceptions, and reported in the standard error reports
    - This should stop bugs inside sniffs causing infinite loops
    - Also stops invalid reports being produced as errors don't print to the screen directly
- Sniff codes are no longer optional
    - If a sniff throws and error or a warning, it must specify an internal code for that message
- The installed_paths config setting can now point directly to a standard
    - Previously, it had to always point to the directory in which the standard lives
- Multiple reports can now be specified using the --report command line argument
    - Report types are separated by commas
    - E.g., --report=full,summary,info
    - Previously, you had to use one argument for each report such as --report=full --report=summary --report=info
- You can now set the severity, message type, and exclude patterns for and entire sniff, category, or standard
    - Previously, this was only available for a single message
- You can now include a single sniff code in a ruleset instead of having to include an entire sniff
    - Including a sniff code will automatically exclude all other messages from that sniff
    - If the sniff is already included by an imported standard, set the sniff severity to 0 and include the specific message you want
- PHPCBF no longer uses patch
    - Files are now always overwritten
    - The --no-patch option has been removed
- Added a --basepath option to strip a directory from the front of file paths in output (request #470)
    - The basepath is absolute or relative to the current directory
    - E.g., to output paths relative to current dir in reports, use --basepath=.
- Ignore rules are now checked when using STDIN (request #733)
- Added an include-pattern tag to rulesets to include a sniff for specific files and folders only (request #656)
    - This is the exact opposite of the exclude-pattern tag
    - This option is only usable within sniffs, not globally like exclude-patterns are
- Added a new -m option to stop error messages from being recorded, which saves a lot of memory
    - PHPCBF always uses this setting to reduce memory as it never outputs error messages
    - Setting the $recordErrors member var inside custom report classes is no longer supported (use -m instead)
- Exit code 2 is now used to indicate fixable errors were found (request #930)
    - Exit code 3 is now used for general script execution errors
    - Exit code 1 is used to indicate that coding standard errors were found, but none are fixable
    - Exit code 0 is unchanged and continues to mean no coding standard errors found

### Removed
- The included PHPCS standard has been removed
    - All rules are now found inside the phpcs.xml.dist file
    - Running "phpcs" without any arguments from a git clone will use this ruleset
- The included SVN pre-commit hook has been removed
    - Hooks for version control systems will no longer be maintained within the PHPCS project


## [2.9.0] - 2017-05-04
### Changed
- Added Generic.Debug.ESLint sniff to run ESLint over JS files and report errors
    - Set eslint path using: phpcs --config-set eslint_path /path/to/eslint
    - Thanks to Ryan McCue for the contribution
- T_POW is now properly considered an arithmetic operator, and will be checked as such
    - Thanks to Juliette Reinders Folmer for the patch
- T_SPACESHIP and T_COALESCE are now properly considered comparison operators, and will be checked as such
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.PHP.DisallowShortOpenTag now warns about possible short open tags even when short_open_tag is set to OFF
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.WhiteSpace.DisallowTabIndent now finds and fixes improper use of spaces anywhere inside the line indent
    - Previously, only the first part of the indent was used to determine the indent type
    - Thanks to Juliette Reinders Folmer for the patch
- PEAR.Commenting.ClassComment now supports checking of traits as well as classes and interfaces
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.Commenting.FunctionCommentThrowTag now supports re-throwing exceptions (request #946)
    - Thanks to Samuel Levy for the patch
- Squiz.PHP.DisallowMultipleAssignments now ignores PHP4-style member var assignments
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.WhiteSpace.FunctionSpacing now ignores spacing above functions when they are preceded by inline comments
    - Stops conflicts between this sniff and comment spacing sniffs
- Squiz.WhiteSpace.OperatorSpacing no longer checks the equal sign in declare statements
    - Thanks to Juliette Reinders Folmer for the patch
- Added missing error codes for a couple of sniffs so they can now be customised as normal

### Fixed
- Fixed bug #1266 : PEAR.WhiteSpace.ScopeClosingBrace can throw an error while fixing mixed PHP/HTML
- Fixed bug #1364 : Yield From values are not recognised as returned values in Squiz FunctionComment sniff
- Fixed bug #1373 : Error in tab expansion results in white-space of incorrect size
    - Thanks to Mark Clements for the patch
- Fixed bug #1381 : Tokenizer: dereferencing incorrectly identified as short array
- Fixed bug #1387 : Squiz.ControlStructures.ControlSignature does not handle alt syntax when checking space after closing   brace
- Fixed bug #1392 : Scope indent calculated incorrectly when using array destructuring
- Fixed bug #1394 : integer type hints appearing as TypeHintMissing instead of ScalarTypeHintMissing
    - PHP 7 type hints were also being shown when run under PHP 5 in some cases
- Fixed bug #1405 : Squiz.WhiteSpace.ScopeClosingBrace fails to fix closing brace within indented PHP tags
- Fixed bug #1421 : Ternaries used in constant scalar expression for param default misidentified by tokenizer
- Fixed bug #1431 : PHPCBF can't fix short open tags when they are not followed by a space
    - Thanks to Gonçalo Queirós for the patch
- Fixed bug #1432 : PHPCBF can make invalid fixes to inline JS control structures that make use of JS objects


## [2.8.1] - 2017-03-02
### Security
- This release contains a fix for a security advisory related to the improper handling of shell commands
    - Uses of shell_exec() and exec() were not escaping filenames and configuration settings in most cases
    - A properly crafted filename or configuration option would allow for arbitrary code execution when using some features
    - All users are encouraged to upgrade to this version, especially if you are checking 3rd-party code
          - e.g., you run PHPCS over libraries that you did not write
          - e.g., you provide a web service that runs PHPCS over user-uploaded files or 3rd-party repositories
          - e.g., you allow external tool paths to be set by user-defined values
    - If you are unable to upgrade but you check 3rd-party code, ensure you are not using the following features:
          - The diff report
          - The notify-send report
          - The Generic.PHP.Syntax sniff
          - The Generic.Debug.CSSLint sniff
          - The Generic.Debug.ClosureLinter sniff
          - The Generic.Debug.JSHint sniff
          - The Squiz.Debug.JSLint sniff
          - The Squiz.Debug.JavaScriptLint sniff
          - The Zend.Debug.CodeAnalyzer sniff
    - Thanks to Klaus Purer for the report

### Changed
- The PHP-supplied T_COALESCE_EQUAL token has been replicated for PHP versions before 7.2
- PEAR.Functions.FunctionDeclaration now reports an error for blank lines found inside a function declaration
- PEAR.Functions.FunctionDeclaration no longer reports indent errors for blank lines in a function declaration
- Squiz.Functions.MultiLineFunctionDeclaration no longer reports errors for blank lines in a function declaration
    - It would previously report that only one argument is allowed per line
- Squiz.Commenting.FunctionComment now corrects multi-line param comment padding more accurately
- Squiz.Commenting.FunctionComment now properly fixes pipe-separated param types
- Squiz.Commenting.FunctionComment now works correctly when function return types also contain a comment
    - Thanks to Juliette Reinders Folmer for the patch
- Squiz.ControlStructures.InlineIfDeclaration now supports the elvis operator
    - As this is not a real PHP operator, it enforces no spaces between ? and : when the THEN statement is empty
- Squiz.ControlStructures.InlineIfDeclaration is now able to fix the spacing errors it reports

### Fixed
- Fixed bug #1340 : STDIN file contents not being populated in some cases
    - Thanks to David Biňovec for the patch
- Fixed bug #1344 : PEAR.Functions.FunctionCallSignatureSniff throws error for blank comment lines
- Fixed bug #1347 : PSR2.Methods.FunctionCallSignature strips some comments during fixing
    - Thanks to Algirdas Gurevicius for the patch
- Fixed bug #1349 : Squiz.Strings.DoubleQuoteUsage.NotRequired message is badly formatted when string contains a CR newline char
    - Thanks to Algirdas Gurevicius for the patch
- Fixed bug #1350 : Invalid Squiz.Formatting.OperatorBracket error when using namespaces
- Fixed bug #1369 : Empty line in multi-line function declaration cause infinite loop


## [2.8.0] - 2017-02-02
### Changed
- The Internal.NoCodeFound error is no longer generated for content sourced from STDIN
    - This should stop some Git hooks generating errors because PHPCS is trying to process the refs passed on STDIN
- Squiz.Commenting.DocCommentAlignment now checks comments on class properties defined using the VAR keyword
    - Thanks to Klaus Purer for the patch
- The getMethodParameters() method now recognises "self" as a valid type hint
    - The return array now contains a new "content" index containing the raw content of the param definition
    - Thanks to Juliette Reinders Folmer for the patch
- The getMethodParameters() method now supports nullable types
    - The return array now contains a new "nullable_type" index set to true or false for each method param
    - Thanks to Juliette Reinders Folmer for the patch
- The getMethodParameters() method now supports closures
    - Thanks to Juliette Reinders Folmer for the patch
- Added more guard code for JS files with syntax errors (request #1271 and request #1272)
- Added more guard code for CSS files with syntax errors (request #1304)
- PEAR.Commenting.FunctionComment fixers now correctly handle multi-line param comments
- AbstractVariableSniff now supports anonymous classes
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.NamingConventions.ConstructorName and PEAR.NamingConventions.ValidVariable now support anonymous classes
- Generic.NamingConventions.CamelCapsFunctionName and PEAR.NamingConventions.ValidFunctionName now support anonymous   classes
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.CodeAnalysis.UnusedFunctionParameter and PEAR.Functions.ValidDefaultValue now support closures
    - Thanks to Juliette Reinders Folmer for the patch
- PEAR.NamingConventions.ValidClassName and Squiz.Classes.ValidClassName now support traits
    - Thanks to Juliette Reinders Folmer for the patch
- Generic.Functions.FunctionCallArgumentSpacing now supports closures other PHP-provided functions
    - Thanks to Algirdas Gurevicius for the patch
- Fixed an error where a nullable type character was detected as an inline then token
    - A new T_NULLABLE token has been added to represent the ? nullable type character
    - Thanks to Jaroslav Hanslík for the patch
- Squiz.WhiteSpace.SemicolonSpacing no longer removes comments while fixing the placement of semicolons
    - Thanks to Algirdas Gurevicius for the patch

### Fixed
- Fixed bug #1230 : JS tokeniser incorrectly tokenises bitwise shifts as comparison
    - Thanks to Ryan McCue for the patch
- Fixed bug #1237 : Uninitialized string offset in PHP Tokenizer on PHP 5.2
- Fixed bug #1239 : Warning when static method name is 'default'
- Fixed bug #1240 : False positive for function names starting with triple underscore
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1245 : SELF is not recognised as T_SELF token in: return new self
- Fixed bug #1246 : A mix of USE statements with and without braces can cause the tokenizer to mismatch brace tokens
    - Thanks to Michał Bundyra for the patch
- Fixed bug #1249 : GitBlame report requires a .git directory
- Fixed bug #1252 : Squiz.Strings.ConcatenationSpacing fix creates syntax error when joining a number to a string
- Fixed bug #1253 : Generic.ControlStructures.InlineControlStructure fix creates syntax error fixing if-try/catch
- Fixed bug #1255 : Inconsistent indentation check results when ELSE on new line
- Fixed bug #1257 : Double dash in CSS class name can lead to "Named colours are forbidden" false positives
- Fixed bug #1260 : Syntax errors not being shown when error_prepend_string is set
    - Thanks to Juliette Reinders Folmer for the patch
- Fixed bug #1264 : Array return type hint is sometimes detected as T_ARRAY_HINT instead of T_RETURN_TYPE
    - Thanks to Jaroslav Hanslík for the patch
- Fixed bug #1265 : ES6 arrow function raises unexpected operator spacing errors
- Fixed bug #1267 : Fixer incorrectly handles filepaths with repeated dir names
    - Thanks to Sergey Ovchinnikov for the patch
- Fixed bug #1276 : Commenting.FunctionComment.InvalidReturnVoid conditional issue with anonymous classes
- Fixed bug #1277 : Squiz.PHP.DisallowMultipleAssignments.Found error when var assignment is on the same line as an   open tag
- Fixed bug #1284 : Squiz.Arrays.ArrayBracketSpacing.SpaceBeforeBracket false positive match for short list syntax


## [2.7.1] - 2016-11-30
### Changed
- Squiz.ControlStructures.ControlSignature.SpaceAfterCloseParenthesis fix now removes unnecessary whitespace
- Squiz.Formatting.OperatorBracket no longer errors for negative array indexes used within a function call
- Squiz.PHP.EmbeddedPhp no longer expects a semicolon after statements that are only opening a scope
- Fixed a problem where the content of T_DOC_COMMENT_CLOSE_TAG tokens could sometimes be (boolean) false
- Developers of custom standards with custom test runners can now have their standards ignored by the built-in test runner
    - Set the value of an environment variable called PHPCS_IGNORE_TESTS with a comma separated list of your standard names
    - Thanks to Juliette Reinders Folmer for the patch
- The unit test runner now loads the test sniff outside of the standard's ruleset so that exclude rules do not get applied
    - This may have caused problems when testing custom sniffs inside custom standards
    - Also makes the unit tests runs a little faster
- The SVN pre-commit hook now works correctly when installed via composer
    - Thanks to Sergey for the patch

### Fixed
- Fixed bug #1135 : PEAR.ControlStructures.MultiLineCondition.CloseBracketNewLine not detected if preceded by multiline function call
- Fixed bug #1138 : PEAR.ControlStructures.MultiLineCondition.Alignment not detected if closing brace is first token on line
- Fixed bug #1141 : Sniffs that check EOF newlines don't detect newlines properly when the last token is a doc block
- Fixed bug #1150 : Squiz.Strings.EchoedStrings does not properly fix bracketed statements
- Fixed bug #1156 : Generic.Formatting.DisallowMultipleStatements errors when multiple short echo tags are used on the same line
    - Thanks to Nikola Kovacs for the patch
- Fixed bug #1161 : Absolute report path is treated like a relative path if it also exists within the current directory
- Fixed bug #1170 : Javascript regular expression literal not recognized after comparison operator
- Fixed bug #1180 : Class constant named FUNCTION is incorrectly tokenized
- Fixed bug #1181 : Squiz.Operators.IncrementDecrementUsage.NoBrackets false positive when incrementing properties
    - Thanks to Jürgen Henge-Ernst for the patch
- Fixed bug #1188 : Generic.WhiteSpace.ScopeIndent issues with inline HTML and multi-line function signatures
- Fixed bug #1190 : phpcbf on if/else with trailing comment generates erroneous code
- Fixed bug #1191 : Javascript sniffer fails with function called "Function"
- Fixed bug #1203 : Inconsistent behavior of PHP_CodeSniffer_File::findEndOfStatement
- Fixed bug #1218 : CASE conditions using class constants named NAMESPACE/INTERFACE/TRAIT etc are incorrectly tokenized
- Fixed bug #1221 : Indented function call with multiple closure arguments can cause scope indent error
- Fixed bug #1224 : PHPCBF fails to fix code with heredoc/nowdoc as first argument to a function


## [0.0.2] - 2006-07-25
### Changed
- Removed the including of checked files to stop errors caused by parsing them
- Removed the use of reflection so checked files do not have to be included
- Memory usage has been greatly reduced
- Much faster tokenising and checking times
- Reworked the PEAR coding standard sniffs (much faster now)
- Fix some bugs with the PEAR scope indentation standard
- Better checking for installed coding standards
- Can now accept multiple files and dirs on the command line
- Added an option to list installed coding standards
- Added an option to print a summary report (number of errors and warnings shown for each file)
- Added an option to hide warnings from reports
- Added an option to print verbose output (so you know what is going on)
- Reordered command line args to put switches first (although order is not enforced)
- Switches can now be specified together (eg. php -nv) as well as separately (phpcs -n -v)


## [0.0.1] - 2006-07-19
### Added
- Initial preview release
