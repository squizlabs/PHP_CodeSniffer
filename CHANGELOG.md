# Changelog
The file documents changes to the PHP_CodeSniffer project.

## [Unreleased]

### Added
- An error message is now displayed if no files were checked during a run
    - This occurs when all of the specified files matched exclusion rules, or none matched filtering rules

### Changes
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

