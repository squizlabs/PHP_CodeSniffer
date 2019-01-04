PHP_CodeSniffer version 1.3.0 contains an important backwards compatibility break that all coding standard authors need to be aware of. Upgrading your coding standard to make use of the new `ruleset.xml` files is an easy process and this guide will show you how to get it done.

> Note: Please note that if you have not created your own coding standard, you do not need to follow this guide. Users of PHP_CodeSniffer that use one of the built-in standards can continue to check their code as normal.

This guide assumes your coding standard has the following directory structure:
```
MyStandard
|_ MyStandardCodingStandard.php
|_ Sniffs
   |_ Commenting
      |_ DisallowHashCommentsSniff.php
```
In this sample coding standard, we have a single sniff being included directly within the standard's sniff directory. But it doesn't matter how many sniffs you have as they are automatically included into the standard in all versions of PHP_CodeSniffer. Your standard may not even have any directly included sniffs, preferring to include sniffs from other standards via the `CodingStandard.php` class.

The only thing we need to do to upgrade a custom coding standard is convert the `CodingStandard.php` class to a `ruleset.xml` file. We can leave all directly included sniffs alone and we don't have to change our directory structure.

### The Basics

The first thing you need to do is create a `ruleset.xml` file directly under your top-level directory. The name of the file must be `ruleset.xml`:
```
touch MyStandard/ruleset.xml
```

The contents of this file will be minimal if you are not including any sniffs from other standards. So the file content would look like this:
```xml
<?xml version="1.0"?>
<ruleset name="My Standard">
    <description>My custom coding standard</description>
</ruleset>
```

A simple `ruleset.xml` file like this tells PHP_CodeSniffer that this directory contains a coding standard, the name of the standard is *My Standard* and the sniffs in the standard are sourced directly from the default `Sniffs` directory.

Once you've created your `ruleset.xml` file, you can go ahead and delete the `CodingStandard.php` class file as it is no longer required. However, you can keep both files in the coding standard if you want to use your standard in both old and new versions of PHP_CodeSniffer. But be aware that you will need to make changes to both files and any advanced ruleset features you add to your `ruleset.xml` file can not be replicated in your `CodingStandard.php` class file.

### A Simple Coding Standard Class

You can build on a coding standard by including sniffs from other standards. A sample `CodingStandard.php` class from the PHP_CodeSniffer documentation contains this:
```php
public function getIncludedSniffs()
{
  return array(
          'PEAR',
          'Generic/Sniffs/Formatting/MultipleStatementAlignmentSniff.php',
          'Generic/Sniffs/Functions',
         );

}//end getIncludedSniffs()
```

The example above includes the `MultipleStatementAlignment` sniff from the `Generic` coding standard, all sniffs in the `Functions` category of the `Generic` coding standard and all sniffs defined in the `PEAR` coding standard.

These rules would be replicated in a `ruleset.xml` file like this:
```xml
<?xml version="1.0"?>
    <ruleset name="My Standard">
    <description>My custom coding standard</description>
    <rule ref="PEAR"/>
    <rule ref="Generic.Formatting.MultipleStatementAlignment"/>
    <rule ref="Generic/Sniffs/Functions"/>
</ruleset>
```

The two changes here are the use of the `rule` tag to include sniffs and standards, and also the way we reference an individual sniff. Instead of specifying the path to the sniff we instead specify the internal code that PHP_CodeSniffer gives it, which is based on the path. It's actually a pretty easy conversion. Just just drop the `Sniffs` directory, convert the slashes to periods and remove `Sniff.php` from the end. Here are some more examples to make sure it is clear.
```
BEFORE: Generic/Sniffs/VersionControl/SubversionPropertiesSniff.php
AFTER:  Generic.VersionControl.SubversionProperties

BEFORE: PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php
AFTER:  PEAR.ControlStructures.ControlSignature

BEFORE: Squiz/Sniffs/Strings/DoubleQuoteUsageSniff.php
AFTER:  Squiz.Strings.DoubleQuoteUsage
```

## Coding Standards with Exclusions

Some coding standards include a set of sniffs from an external standard but then exclude some specific sniffs. A sample `CodingStandard.php` class with exclusions from the PHP_CodeSniffer documentation contains this:
```php
public function getIncludedSniffs()
{
  return array(
          'PEAR',
         );

}//end getIncludedSniffs()

public function getExcludedSniffs()
{
  return array(
          'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
         );

}//end getExcludedSniffs()
```

The example above includes the whole `PEAR` coding standard except for the `ControlSignature` sniff.

These rules would be replicated in a `ruleset.xml` file like this:
```xml
<?xml version="1.0"?>
<ruleset name="My Standard">
    <description>My custom coding standard</description>
    <rule ref="PEAR">
        <exclude name="PEAR.ControlStructures.ControlSignature"/>
    </rule>
</ruleset>
```

Notice how the exclusions are grouped with the standard they are being excluded from and how they again use the internal sniff codes instead of full paths to sniff files.

## A Practical Example

PHP_CodeSniffer comes with a coding standard called `PHPCS`, which is the entire `PEAR` standard as well as some `Squiz` sniffs thrown in. In version 1.2.2, the `PHPCSCodingStandard.php` file contained these two functions:
```php
public function getIncludedSniffs()
{
  return array(
          'PEAR',
          'Squiz',
         );

}//end getIncludedSniffs()

public function getExcludedSniffs()
{
  return array(
          'Squiz/Sniffs/Classes/ClassFileNameSniff.php',
          'Squiz/Sniffs/Classes/ValidClassNameSniff.php',
          'Squiz/Sniffs/Commenting/ClassCommentSniff.php',
          'Squiz/Sniffs/Commenting/FileCommentSniff.php',
          'Squiz/Sniffs/Commenting/FunctionCommentSniff.php',
          'Squiz/Sniffs/Commenting/VariableCommentSniff.php',
          'Squiz/Sniffs/ControlStructures/SwitchDeclarationSniff.php',
          'Squiz/Sniffs/Files/FileExtensionSniff.php',
          'Squiz/Sniffs/NamingConventions/ConstantCaseSniff.php',
          'Squiz/Sniffs/WhiteSpace/ScopeIndentSniff.php',
         );

}//end getExcludedSniffs()
```

To support version 1.3.0, the `ruleset.xml` file was created with this content:
```xml
<?xml version="1.0"?>
<ruleset name="PHP_CodeSniffer">
    <description>The coding standard for PHP_CodeSniffer.</description>

    <!-- Include the whole PEAR standard -->
    <rule ref="PEAR"/>

    <!-- Include most of the Squiz standard -->
    <rule ref="Squiz">
        <exclude name="Squiz.Classes.ClassFileName"/>
        <exclude name="Squiz.Classes.ValidClassName"/>
        <exclude name="Squiz.Commenting.ClassComment"/>
        <exclude name="Squiz.Commenting.FileComment"/>
        <exclude name="Squiz.Commenting.FunctionComment"/>
        <exclude name="Squiz.Commenting.VariableComment"/>
        <exclude name="Squiz.ControlStructures.SwitchDeclaration"/>
        <exclude name="Squiz.Files.FileExtension"/>
        <exclude name="Squiz.NamingConventions.ConstantCase"/>
        <exclude name="Squiz.WhiteSpace.ScopeIndent"/>
    </rule>
</ruleset>
```