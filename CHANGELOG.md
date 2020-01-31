# Changelog
The file documents changes to the PHP_CodeSniffer project.

## [Unreleased]

### Changes
- The `--extensions` command line argument no longer accepts the tokenizer along with the extension
    - Previously, you would check `.module` files as PHP files using `--extensions=module/php`
    - Now, you use `--extensions=module`
- Composer installs no longer include any test files

### Removed
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
- Removed sniff `Generic.Debug.ClosureLinter`
- Removed sniff `Generic.Debug.CSSLint`
- Removed sniff `Generic.Debug.ESLint`
- Removed sniff `Generic.Debug.JSHint`
- Removed sniff `Squiz.Classes.DuplicateProperty`
- Removed sniff `Squiz.Debug.JavaScriptLint`
- Removed sniff `Squiz.Debug.JSLint`
- Removed sniff `Squiz.Objects.DisallowObjectStringIndex`
- Removed sniff `Squiz.Objects.ObjectMemberComment`
- Removed sniff `Squiz.WhiteSpace.PropertyLabelSpacing`
- Removed the entire `Squiz.CSS` category, and sniffs within
- Removed the entire `MySource` standard, and all sniffs within
