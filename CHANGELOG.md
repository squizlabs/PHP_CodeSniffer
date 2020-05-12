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
