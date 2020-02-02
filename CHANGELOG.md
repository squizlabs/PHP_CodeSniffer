# Changelog
The file documents changes to the PHP_CodeSniffer project.

## [Unreleased]

### Changes
- The default coding standard has changed from `PEAR` to `PSR12`
- The `--extensions` command line argument no longer accepts the tokenizer along with the extension
    - Previously, you would check `.module` files as PHP files using `--extensions=module/php`
    - Now, you use `--extensions=module`
- None of the included sniffs will warn about possible parse errors any more
    - This improves the experience when the file is being checked inside an editor during live coding
    - If you want to detect parse errors, use a linter instead
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
