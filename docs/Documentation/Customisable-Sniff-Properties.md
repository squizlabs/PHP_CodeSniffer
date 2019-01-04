The behaviour of some sniffs can be changed by setting certain sniff properties in your ruleset.xml file. This page lists the sniff properties that are available for customisation. For properties that were added after ruleset support was introduced in version 1.3.0, the first stable version that made the property available is listed.

For more information about changing sniff behaviour by customising your ruleset, see the [[Annotated ruleset]].

## Table of contents
* Generic Sniffs
    * [Generic.Arrays.ArrayIndent](#genericarraysarrayindent)
    * [Generic.ControlStructures.InlineControlStructure](#genericcontrolstructuresinlinecontrolstructure)
    * [Generic.Debug.ClosureLinter](#genericdebugclosurelinter)
    * [Generic.Debug.ESLint](#genericdebugeslint)
    * [Generic.Files.LineEndings](#genericfileslineendings)
    * [Generic.Files.LineLength](#genericfileslinelength)
    * [Generic.Formatting.MultipleStatementAlignment](#genericformattingmultiplestatementalignment)
    * [Generic.Formatting.SpaceAfterCast](#genericformattingspaceaftercast)
    * [Generic.Formatting.SpaceAfterNot](#genericformattingspaceafternot)
    * [Generic.Functions.OpeningFunctionBraceBsdAllman](#genericfunctionsopeningfunctionbracebsdallman)
    * [Generic.Functions.OpeningFunctionBraceKernighanRitchie](#genericfunctionsopeningfunctionbracekernighanritchie)
    * [Generic.Metrics.CyclomaticComplexity](#genericmetricscyclomaticcomplexity)
    * [Generic.Metrics.NestingLevel](#genericmetricsnestinglevel)
    * [Generic.NamingConventions.CamelCapsFunctionName](#genericnamingconventionscamelcapsfunctionname)
    * [Generic.PHP.ForbiddenFunctions](#genericphpforbiddenfunctions)
    * [Generic.PHP.NoSilencedErrors](#genericphpnosilencederrors)
    * [Generic.Strings.UnnecessaryStringConcat](#genericstringsunnecessarystringconcat)
    * [Generic.WhiteSpace.ArbitraryParenthesesSpacing](#genericwhitespacearbitraryparenthesesspacing)
    * [Generic.WhiteSpace.ScopeIndent](#genericwhitespacescopeindent)
* PEAR Sniffs
    * [PEAR.ControlStructures.ControlSignature](#pearcontrolstructurescontrolsignature)
    * [PEAR.ControlStructures.MultiLineCondition](#pearcontrolstructuresmultilinecondition)
    * [PEAR.Formatting.MultiLineAssignment](#pearformattingmultilineassignment)
    * [PEAR.Functions.FunctionCallSignature](#pearfunctionsfunctioncallsignature)
    * [PEAR.Functions.FunctionDeclaration](#pearfunctionsfunctiondeclaration)
    * [PEAR.WhiteSpace.ObjectOperatorIndent](#pearwhitespaceobjectoperatorindent)
    * [PEAR.WhiteSpace.ScopeClosingBrace](#pearwhitespacescopeclosingbrace)
    * [PEAR.WhiteSpace.ScopeIndent](#pearwhitespacescopeindent)
* PSR2 Sniffs
    * [PSR2.Classes.ClassDeclaration](#psr2classesclassdeclaration)
    * [PSR2.ControlStructures.ControlStructureSpacing](#psr2controlstructurescontrolstructurespacing)
    * [PSR2.ControlStructures.SwitchDeclaration](#psr2controlstructuresswitchdeclaration)
    * [PSR2.Methods.FunctionCallSignature](#psr2methodsfunctioncallsignature)
* PSR12 Sniffs
    * [PSR12.Namespaces.CompoundNamespaceDepth](#psr12namespacescompoundnamespacedepth)
* Squiz Sniffs
    * [Squiz.Classes.ClassDeclaration](#squizclassesclassdeclaration)
    * [Squiz.Commenting.LongConditionClosingComment](#squizcommentinglongconditionclosingcomment)
    * [Squiz.ControlStructures.ControlSignature](#squizcontrolstructurescontrolsignature)
    * [Squiz.ControlStructures.ForEachLoopDeclaration](#squizcontrolstructuresforeachloopdeclaration)
    * [Squiz.ControlStructures.ForLoopDeclaration](#squizcontrolstructuresforloopdeclaration)
    * [Squiz.ControlStructures.SwitchDeclaration](#squizcontrolstructuresswitchdeclaration)
    * [Squiz.CSS.ForbiddenStyles](#squizcssforbiddenstyles)
    * [Squiz.CSS.Indentation](#squizcssindentation)
    * [Squiz.Functions.FunctionDeclaration](#squizfunctionsfunctiondeclaration)
    * [Squiz.Functions.FunctionDeclarationArgumentSpacing](#squizfunctionsfunctiondeclarationargumentspacing)
    * [Squiz.PHP.CommentedOutCode](#squizphpcommentedoutcode)
    * [Squiz.PHP.DiscouragedFunctions](#squizphpdiscouragedfunctions)
    * [Squiz.PHP.ForbiddenFunctions](#squizphpforbiddenfunctions)
    * [Squiz.Strings.ConcatenationSpacing](#squizstringsconcatenationspacing)
    * [Squiz.WhiteSpace.FunctionSpacing](#squizwhitespacefunctionspacing)
    * [Squiz.WhiteSpace.MemberVarSpacing](#squizwhitespacemembervarspacing)
    * [Squiz.WhiteSpace.ObjectOperatorSpacing](#squizwhitespaceobjectoperatorspacing)
    * [Squiz.WhiteSpace.OperatorSpacing](#squizwhitespaceoperatorspacing)
    * [Squiz.WhiteSpace.SuperfluousWhitespace](#squizwhitespacesuperfluouswhitespace)

***

## Generic Sniffs

### Generic.Arrays.ArrayIndent

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 3.2.0

One of the rules that this sniff enforces is the indent of keys in a multi-line array declaration. By default, this sniff ensures that each key is indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="Generic.Arrays.ArrayIndent">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### Generic.ControlStructures.InlineControlStructure

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
error         | bool | true    | -

If the `error` property is set to `false`, a warning will be thrown for violations instead of an error.

```xml
<rule ref="Generic.ControlStructures.InlineControlStructure">
    <properties>
        <property name="error" value="false" />
    </properties>
</rule>
```

### Generic.Debug.ClosureLinter

Property Name | Type  | Default | Available Since
------------- | ----- | ------- | ---------------
errorCodes    | array | -       | -
ignoreCodes   | array | -       | -

The `Generic.Debug.ClosureLinter` sniff runs the [Google Closure Linter](https://github.com/google/closure-linter) tool over JavaScript files and reports errors that the tool finds. All found errors are reported as PHP_CodeSniffer warnings by default.

There are two configurable options:
* `errorCodes` : a list of error codes that should show as errors instead of warnings
* `ignoreCodes` : a list of error codes that should be ignored

> Note: The error codes accepted by this sniff are the 4-digit codes generated by the `gjslint` tool and displayed in the warning messages produced by this sniff.

```xml
<rule ref="Generic.Debug.ClosureLinter">
    <properties>
        <property name="errorCodes" type="array" value="0210"/>
        <property name="ignoreCodes" type="array" value="0001,0110,0240"/>
    </properties>
</rule>
```

### Generic.Debug.ESLint

Property Name | Type   | Default | Available Since
------------- | ------ | ------- | ---------------
configFile    | string | -       | 2.9.0

The `Generic.Debug.ESLint` sniff runs the [ESLint](https://eslint.org/) tool over JavaScript files and reports errors that the tool finds. All found violations are reported as either PHP_CodeSniffer errors or warnings based on the severity level that the ESLint tool provides.

The sniff will attempt to auto-discover an ESLint config file in the current directory, but a config file path can also be specified by setting the `configFile` property.

```xml
<rule ref="Generic.Debug.ESLint">
    <properties>
        <property name="configFile" value="/path/to/.eslintrc.json"/>
    </properties>
</rule>
```

### Generic.Files.LineEndings

Property Name | Type   | Default | Available Since
------------- | ------ | ------- | ---------------
eolChar       | string | \n      | -

This sniff ensures that files use a specific line ending, which can be customised by setting the `eolChar` property.

```xml
<rule ref="Generic.Files.LineEndings">
    <properties>
        <property name="eolChar" value="\r\n" />
    </properties>
</rule>
```

### Generic.Files.LineLength

Property Name     | Type  | Default | Available Since
----------------- | ----  | ------- | ---------------
lineLimit         | int   | 80      | -
absoluteLineLimit | int   | 100     | -
ignoreComments    | bool  | false   | 3.1.0

This sniff checks all lines in a file and generates warnings if they are over `lineLimit` characters in length and errors if they are over `absoluteLineLimit` in length. These properties can be used to set the threshold at which errors are reported.

> Note: The value of the `lineLimit` property should be less than or equal to the value of the `absoluteLineLimit` property.

```xml
<!--
 Warn about lines longer than 100 chars,
 and error for lines longer than 135 chars.
-->
<rule ref="Generic.Files.LineLength">
    <properties>
        <property name="lineLimit" value="100" />
        <property name="absoluteLineLimit" value="135" />
    </properties>
</rule>
```

If errors are not required, the value of `absoluteLineLimit` can be set to zero.

```xml
<!-- Warn about lines longer than 135 chars, and never error. -->
<rule ref="Generic.Files.LineLength">
    <properties>
        <property name="lineLimit" value="135" />
        <property name="absoluteLineLimit" value="0" />
    </properties>
</rule>
```

If the `ignoreComments` property is set to `true`, no error or warning will be thrown for a line that only contains a comment, no matter how long the line is.

```xml
<rule ref="Generic.Files.LineLength">
    <properties>
        <property name="ignoreComments" value="true" />
    </properties>
</rule>
```

### Generic.Formatting.MultipleStatementAlignment

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
maxPadding    | int  | 1000    | -
error         | bool | false   | -

This sniff checks the alignment of assignment operators. If there are multiple adjacent assignments, it checks that the equals signs of each assignment are aligned.

The difference in alignment between two adjacent assignments is occasionally quite large, so aligning equals signs would create extremely long lines. By setting the `maxPadding` property, you can configure the maximum amount of padding required to align the assignment with the surrounding assignments before the alignment is ignored and no warnings will be generated.

```xml
<rule ref="Generic.Formatting.MultipleStatementAlignment">
    <properties>
        <property name="maxPadding" value="50" />
    </properties>
</rule>
```

If the `error` property is set to `true`, an error will be thrown for violations instead of a warning.

```xml
<rule ref="Generic.Formatting.MultipleStatementAlignment">
    <properties>
        <property name="error" value="true" />
    </properties>
</rule>
```

### Generic.Formatting.SpaceAfterCast

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
spacing        | int  | 1       | 3.4.0
ignoreNewlines | bool | false   | 3.4.0

This sniff checks the spacing after a type cast. By default, the sniff ensures there is one space after the cast, as shown in the following code snippet:

```php
$var = (int) $foo;
```

Another common way of type casting is to follow the cast with no space, as shown in the following code snippet:

```php
$var = (int)$foo;
```

If you prefer to write your code like this, you can set the `spacing` property to `0`, or whatever padding you prefer.

```xml
<rule ref="Generic.Formatting.SpaceAfterCast">
    <properties>
        <property name="spacing" value="0" />
    </properties>
</rule>
```

Sometimes complex statements are broken over multiple lines for readability. By default, this sniff will generate an error if the type cast is followed by a newline. Setting the `ignoreNewlines` property to `true` will allow newline characters after a type cast.

```xml
<rule ref="Generic.Formatting.SpaceAfterCast">
    <properties>
        <property name="ignoreNewlines" value="true" />
    </properties>
</rule>
```

### Generic.Formatting.SpaceAfterNot

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
spacing        | int  | 1       | 3.4.0
ignoreNewlines | bool | false   | 3.4.0

This sniff checks the spacing after a `!` operator. By default, the sniff ensures there is one space after the operator, as shown in the following code snippet:

```php
if (! $foo) {
}
```

Another common way of using the `!` operator is to follow it with no space, as shown in the following code snippet:

```php
if (!$foo) {
}
```

If you prefer to write your code like this, you can set the `spacing` property to `0`, or whatever padding you prefer.

```xml
<rule ref="Generic.Formatting.SpaceAfterNot">
    <properties>
        <property name="spacing" value="0" />
    </properties>
</rule>
```

Sometimes complex statements are broken over multiple lines for readability, as shown in the following code snippet:
```php
if (!
    ($foo || $bar)
) {
}
```

By default, this sniff will generate an error if the `!` operator is followed by a newline. Setting the `ignoreNewlines` property to `true` will allow newline characters after a `!` operator.

```xml
<rule ref="Generic.Formatting.SpaceAfterNot">
    <properties>
        <property name="ignoreNewlines" value="true" />
    </properties>
</rule>
```

### Generic.Functions.OpeningFunctionBraceBsdAllman

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
checkFunctions | bool | true    | 2.3.0
checkClosures  | bool | false   | 2.3.0

The sniff checks the position of the opening brace of a function and/or closure (anonymous function). The sniff only checks functions by default, but the `checkFunctions` and `checkClosures` properties can be used to have the sniff check one or both of these code blocks.

```xml
<!-- Don't check function braces, but check closure braces. -->
<rule ref="Generic.Functions.OpeningFunctionBraceBsdAllman">
    <properties>
        <property name="checkFunctions" value="false" />
        <property name="checkClosures" value="true" />
    </properties>
</rule>
```

### Generic.Functions.OpeningFunctionBraceKernighanRitchie

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
checkFunctions | bool | true    | 2.3.0
checkClosures  | bool | false   | 2.3.0

The sniff checks the position of the opening brace of a function and/or closure (anonymous function). The sniff only checks functions by default, but the `checkFunctions` and `checkClosures` properties can be used to have the sniff check one or both of these code blocks.

```xml
<!-- Don't check function braces, but check closure braces. -->
<rule ref="Generic.Functions.OpeningFunctionBraceKernighanRitchie">
    <properties>
        <property name="checkFunctions" value="false" />
        <property name="checkClosures" value="true" />
    </properties>
</rule>
```

### Generic.Metrics.CyclomaticComplexity

Property Name      | Type | Default | Available Since
------------------ | ---- | ------- | ---------------
complexity         | int  | 10      | -
absoluteComplexity | int  | 20      | -

This sniff checks the cyclomatic complexity for functions by counting the different paths the function includes.

There are two configurable options:
* `complexity` : the cyclomatic complexity above which this sniff will generate warnings
* `absoluteComplexity` : the cyclomatic complexity above which this sniff will generate errors

> Note: The value of the `complexity` property should be less than or equal to the value of the `absoluteComplexity` property.

```xml
<rule ref="Generic.Metrics.CyclomaticComplexity">
    <properties>
        <property name="complexity" value="15" />
        <property name="absoluteComplexity" value="30" />
    </properties>
</rule>
```

### Generic.Metrics.NestingLevel

Property Name        | Type | Default | Available Since
-------------------- | ---- | ------- | ---------------
nestingLevel         | int  | 5       | -
absoluteNestingLevel | int  | 10      | -

This sniff checks how many level deep that code is nested within a function.

There are two configurable options:
* `nestingLevel` : the nesting level above which this sniff will generate warnings
* `absoluteNestingLevel` : the nesting level above which this sniff will generate errors

```xml
<rule ref="Generic.Metrics.NestingLevel">
    <properties>
        <property name="nestingLevel" value="8" />
        <property name="absoluteNestingLevel" value="12" />
    </properties>
</rule>
```

### Generic.NamingConventions.CamelCapsFunctionName

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
strict        | bool | true    | 1.3.5

This sniff ensures function and method names are in CamelCaps.

Strictly speaking, a name cannot have two capital letters next to each other in CamelCaps format. By setting the `strict` property to `false`, the sniff applies the rule more leniently and allows for two capital letters next to each other in function and method names.

```xml
<rule ref="Generic.NamingConventions.CamelCapsFunctionName">
    <properties>
        <property name="strict" value="false" />
    </properties>
</rule>
```

### Generic.PHP.ForbiddenFunctions

Property Name      | Type  | Default                     | Available Since
-------------------| ----- | --------------------------- | ---------------
forbiddenFunctions | array | sizeof=>count,delete=>unset | 2.0.0
error              | bool  | true                        | -

This sniff discourages the use of alias functions that are kept in PHP for compatibility with older versions. The sniff can be used to forbid the use of any function by setting the `forbiddenFunctions` property. The property is defined as an array, with the keys being the names of the functions to forbid and the values being the names of suggested alternative functions to use instead. If no alternative function exists (i.e., the function should never be used) specify `null` as the value.

```xml
<rule ref="Generic.PHP.ForbiddenFunctions">
    <properties>
        <property name="forbiddenFunctions" type="array"
            value="print=>echo,create_function=>null" />
     </properties>
</rule>
```

If the `error` property is set to `false`, a warning will be thrown for violations instead of an error.

```xml
<rule ref="Generic.PHP.ForbiddenFunctions">
    <properties>
        <property name="error" value="false" />
    </properties>
</rule>
```

### Generic.PHP.NoSilencedErrors

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
error         | bool | true    | -

If the `error` property is set to `false`, a warning will be thrown for violations instead of an error.

```xml
<rule ref="Generic.PHP.NoSilencedErrors">
    <properties>
        <property name="error" value="false" />
    </properties>
</rule>
```

### Generic.Strings.UnnecessaryStringConcat

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
allowMultiline | bool | false   | 2.3.4
error          | bool | true    | -

This sniff checks that two strings using the same quoting style are not concatenated. Sometimes long strings are broken over multiple lines to work within a maximum line length, but this sniff will generate an error for these cases by default. Setting the `allowMultiline` property to `true` will get the sniff to allow string concatenation if the string covers multiple lines.

```xml
<rule ref="Generic.Strings.UnnecessaryStringConcat">
    <properties>
        <property name="allowMultiline" value="true" />
    </properties>
</rule>
```

If the `error` property is set to `false`, a warning will be thrown for violations instead of an error.

```xml
<rule ref="Generic.Strings.UnnecessaryStringConcat">
    <properties>
        <property name="error" value="false" />
    </properties>
</rule>
```

### Generic.WhiteSpace.ArbitraryParenthesesSpacing

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
spacing        | int  | 0       | 3.3.0
ignoreNewlines | bool | false   | 3.3.0

This sniff checks the padding inside parenthesis that are not being used by function declarations, function calls, or control structures. By default, the sniff ensures there are zero spaces inside the parenthesis, as shown in the following code snippet:

```php
$foo = ($bar !== 'bar');
```

Another common way of padding parenthesis is to use a single space, as shown in the following code snippet:

```php
$foo = ( $bar !== 'bar' );
```

If you prefer to write your code like this, you can set the `spacing` property to `1`, or whatever padding you prefer.

```xml
<rule ref="Generic.WhiteSpace.ArbitraryParenthesesSpacing">
    <properties>
        <property name="spacing" value="1" />
    </properties>
</rule>
```

Sometimes long statements are broken over multiple lines to work within a maximum line length, but this sniff will generate an error for these cases by default. Setting the `ignoreNewlines` property to `true` will allow newline characters inside parenthesis, and any required padding for alignment.

```xml
<rule ref="Generic.WhiteSpace.ArbitraryParenthesesSpacing">
    <properties>
        <property name="ignoreNewlines" value="true" />
    </properties>
</rule>
```

### Generic.WhiteSpace.ScopeIndent

Property Name           | Type  | Default | Available Since
----------------------- | ----- | ------- | ---------------
indent                  | int   | 4       | -
exact                   | bool  | false   | -
tabIndent               | bool  | false   | 2.0.0
ignoreIndentationTokens | array | -       | 1.4.8

This sniff checks that code blocks are indented correctly. By default, this sniff ensures that code blocks are indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="Generic.WhiteSpace.ScopeIndent">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

The `exact` property is used to determine whether an indent is treated as an exact number or as a minimum amount. By default, code blocks must be indented at least `indent` spaces from the last code block. If `exact` is set to `true`, code blocks must be indented exactly `indent` spaces from the last code block.

> Note: Enforcing exact indent checking is generally not advised because it doesn't allow for any flexibility when indenting and aligning code. It is almost always better to use the default value and then allow other sniffs to enforce specific indenting rules.

```xml
<rule ref="Generic.WhiteSpace.ScopeIndent">
    <properties>
        <property name="exact" value="true" />
    </properties>
</rule>
```

By default, this sniff enforces the use of spaces for indentation and also uses spaces when fixing the indentation of code blocks. If you prefer using tabs, you can set the `tabIndent` property to `true`. 

> Note: The size of each tab is important, so it should be specified using the `--tab-width` CLI argument or by adding `<arg name="tab-width" value="4"/>` to your ruleset. This sniff will use this value when checking and fixing indents.

```xml
<!-- Tabs should represent 4 spaces. -->
<arg name="tab-width" value="4"/>
...
<!-- Indent using tabs. -->
<rule ref="Generic.WhiteSpace.ScopeIndent">
    <properties>
        <property name="tabIndent" value="true" />
    </properties>
</rule>
```

Setting the `ignoreIndentationTokens` property provides the sniff with a list of tokens that do not need to be checked for indentation. This is commonly used to ignore indentation for code structures such as comments and here/nowdocs.

```xml
<rule ref="Generic.WhiteSpace.ScopeIndent">
    <properties>
        <property name="ignoreIndentationTokens" type="array"
            value="T_COMMENT,T_DOC_COMMENT_OPEN_TAG"/>
    </properties>
</rule>
```




## PEAR Sniffs

### PEAR.ControlStructures.ControlSignature

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | --------------
ignoreComments | bool | true    | 1.4.0

> Note: The `ignoreComments` property is inherited from the AbstractPattern sniff.

This sniff verifies that control structures match a specific pattern of whitespace and bracket placement. By default, comments placed within the declaration will generate an error, but the sniff can be told to ignore comments by setting the `ignoreComments` property to `true`.

```xml
<rule ref="PEAR.ControlStructures.ControlSignature">
    <properties>
        <property name="ignoreComments" value="false" />
    </properties>
</rule>
```

### PEAR.ControlStructures.MultiLineCondition

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.4.7

One of the rules that this sniff enforces is the indent of a condition that has been split over multiple lines. By default, this sniff ensures that each line of the condition is indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PEAR.ControlStructures.MultiLineCondition">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PEAR.Formatting.MultiLineAssignment

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.4.7

One of the rules that this sniff enforces is the indent of an assignment that has been split over multiple lines. By default, this sniff ensures that the line with the assignment operator is indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PEAR.Formatting.MultiLineAssignment">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PEAR.Functions.FunctionCallSignature

Property Name             | Type | Default | Available Since
------------------------- | ---- | ------- | ---------------
indent                    | int  | 4       | 1.3.4
allowMultipleArguments    | bool | true    | 1.3.6
requiredSpacesAfterOpen   | int  | 0       | 1.5.2
requiredSpacesBeforeClose | int  | 0       | 1.5.2

One of the rules this sniff enforces is that function calls have the correct padding inside their bracketed argument lists. By default, the sniff ensures there are zero spaces following the opening bracket, and zero spaces preceding the closing bracket, as shown in the following code snippet:

```php
$foo = getValue($a, $b, $c);
```

Another common way of padding function calls is to use a single space, as shown in the following code snippet:

```php
$foo = getValue( $a, $b, $c );
```

If you prefer to write your code like this, you can set the `requiredSpacesAfterOpen` and `requiredSpacesBeforeClose` properties to `1`, or whatever padding you prefer.

```xml
<rule ref="PEAR.Functions.FunctionCallSignature">
    <properties>
        <property name="requiredSpacesAfterOpen" value="1" />
        <property name="requiredSpacesBeforeClose" value="1" />
    </properties>
</rule>
```

This sniff also enforces the formatting of multi-line function calls. By default, multiple arguments can appear on each line, as shown in the following code snippet:

```php
$returnValue = foo(
    $a, $b, $c,
    $d, $e
);
```

Another common way of defining multi-line function calls is to have one argument per line, as shown in the following code snippet:

```php
$returnValue = foo(
    $a,
    $b,
    $c,
    $d,
    $e
);
```

If you prefer to write your code like this, you can set the `allowMultipleArguments` property to `false`.

```xml
<rule ref="PEAR.Functions.FunctionCallSignature">
    <properties>
        <property name="allowMultipleArguments" value="false" />
    </properties>
</rule>
```

By default, this sniff ensures that each line in a multi-line function call is indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PEAR.Functions.FunctionCallSignature">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PEAR.Functions.FunctionDeclaration

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.4.7

One of the rules that this sniff enforces is the indent of each function argument in a multi-line function declaration. By default, this sniff ensures that each line is indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PEAR.Functions.FunctionDeclaration">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PEAR.WhiteSpace.ObjectOperatorIndent

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.4.6

One of the rules that this sniff enforces is the indent of each line in a multi-line object chain. By default, this sniff ensures that each line is indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PEAR.WhiteSpace.ObjectOperatorIndent">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PEAR.WhiteSpace.ScopeClosingBrace

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.3.4

One of the rules that this sniff enforces is the indent of the case terminating statement. By default, this sniff ensures that the statement is indented 4 spaces from the `case` or `default` keyword, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PEAR.WhiteSpace.ScopeClosingBrace">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PEAR.WhiteSpace.ScopeIndent

Property Name           | Type  | Default | Available Since
----------------------- | ----- | ------- | ---------------
indent                  | int   | 4       | -
exact                   | bool  | false   | -
tabIndent               | bool  | false   | 2.0.0
ignoreIndentationTokens | array | -       | 1.4.8

> Note: All properties are inherited from the [Generic.WhiteSpace.ScopeIndent](#genericwhitespacescopeindent) sniff.

See the [Generic.WhiteSpace.ScopeIndent](#genericwhitespacescopeindent) sniff for an explanation of all properties.

```xml
<!-- Tabs should represent 4 spaces. -->
<arg name="tab-width" value="4"/>
...
<rule ref="PEAR.WhiteSpace.ScopeIndent">
    <properties>
        <property name="exact" value="true" />
        <property name="tabIndent" value="true" />
        <property name="ignoreIndentationTokens" type="array"
            value="T_COMMENT,T_DOC_COMMENT_OPEN_TAG"/>
    </properties>
</rule>
```




## PSR2 Sniffs

### PSR2.Classes.ClassDeclaration

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
indent        | int  | 4       | 1.3.5

One of the rules that this sniff enforces is the indent of a list of implemented or extended class names that have been split over multiple lines. By default, this sniff ensures that the class names are indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PSR2.Classes.ClassDeclaration">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PSR2.ControlStructures.ControlStructureSpacing

Property Name             | Type | Default | Available Since
------------------------- | ---- | ------- | ---------------
requiredSpacesAfterOpen   | int  | 0       | 1.5.2
requiredSpacesBeforeClose | int  | 0       | 1.5.2

This sniff checks that control structures have the correct padding inside their bracketed statement. By default, the sniff ensures there are zero spaces following the opening bracket, and zero spaces preceding the closing bracket, as shown in the following code snippet:

```php
if ($condition === true) {
    // Body.
}
```

Another common way of padding control structures is to use a single space, as shown in the following code snippet:

```php
if ( $condition === true ) {
    // Body.
}
```

If you prefer to write your code like this, you can set the `requiredSpacesAfterOpen` and `requiredSpacesBeforeClose` properties to `1`, or whatever padding you prefer.

```xml
<rule ref="PSR2.ControlStructures.ControlStructureSpacing">
    <properties>
        <property name="requiredSpacesAfterOpen" value="1" />
        <property name="requiredSpacesBeforeClose" value="1" />
    </properties>
</rule>
```

### PSR2.ControlStructures.SwitchDeclaration

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.4.5

One of the rules that this sniff enforces is the indent of the case terminating statement. By default, this sniff ensures that the statement is indented 4 spaces from the `case` or `default` keyword, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="PSR2.ControlStructures.SwitchDeclaration">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### PSR2.Methods.FunctionCallSignature

Property Name             | Type | Default | Available Since
------------------------- | ---- | ------- | ---------------
indent                    | int  | 4       | 1.3.4
allowMultipleArguments    | bool | false   | 1.4.7
requiredSpacesAfterOpen   | int  | 0       | 1.5.2
requiredSpacesBeforeClose | int  | 0       | 1.5.2

> Note: All properties are inherited from the [PEAR.Functions.FunctionCallSignature](#pearfunctionsfunctioncallsignature) sniff, although the default value of `allowMultipleArguments` is changed.

See the [PEAR.Functions.FunctionCallSignature](#pearfunctionsfunctioncallsignature) sniff for an explanation of all properties.




## PSR12 Sniffs

### PSR12.Namespaces.CompoundNamespaceDepth

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
maxDepth      | int  | 2       | 3.3.0

This sniff checks the depth of imported namespaces inside compound use statements. By default, this sniff ensures that the namespaces are no more than two levels deep, but you can change the depth limit by setting the `maxDepth` property.

```xml
<rule ref="PSR12.Namespaces.CompoundNamespaceDepth">
    <properties>
        <property name="maxDepth" value="4" />
    </properties>
</rule>
```




## Squiz Sniffs

### Squiz.Classes.ClassDeclaration

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.3.5

> Note: The `indent` property is inherited from the [PSR2.Classes.ClassDeclaration](#psr2classesclassdeclaration) sniff.

One of the rules that this sniff enforces is the indent of a list of implemented or extended class names that have been split over multiple lines. By default, this sniff ensures that the class names are indented 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="Squiz.Classes.ClassDeclaration">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### Squiz.Commenting.LongConditionClosingComment

Property Name | Type   | Default  | Available Since
------------- | ------ | -------- | ---------------
lineLimit     | int    | 20       | 2.7.0
commentFormat | string | //end %s | 2.7.0

This sniff checks that long blocks of code have a closing comment. The `lineLimit` property allows you to configure the numbers of lines that the code block must span before requiring a comment. By default, the code block must be at least 20 lines long, including the opening and closing lines, but you can change the required length by setting the `lineLimit` property.

```xml
<rule ref="Squiz.Commenting.LongConditionClosingComment">
    <properties>
        <property name="lineLimit" value="40" />
    </properties>
</rule>
```

When a closing comment is required, the format defaults to `//end %s`, where the %s placeholder is replaced with the type of the code block. For example, `//end if`, `//end foreach`, or `//end switch`. You can change the format of the end comment by setting the `commentFormat` property.

```xml
<!-- Have code block comments look like // end foreach() etc. -->
<rule ref="Squiz.Commenting.LongConditionClosingComment">
    <properties>
        <property name="commentFormat" value="// end %s()" />
    </properties>
</rule>
```

### Squiz.ControlStructures.ControlSignature

Property Name             | Type | Default | Available Since
------------------------- | ---- | ------- | ---------------
requiredSpacesBeforeColon | int  | 1       | 3.2.0

One of the rules this sniff enforces is the number of spaces before the opening brace of control structures. By default, the sniff ensures there is one space before the opening brace for control structures using standard syntax, and one space before the colon for control structures using alternative syntax, as shown in the following code snippet:

```php
if ($foo) :
    // IF body.
else :
    // ELSE body.
endif;
```

A common way of defining control structures using alternative syntax is to put no padding before the colon, as shown in the following code snippet:

```php
if ($foo):
    // IF body.
else:
    // ELSE body.
endif;
```

If you prefer to write your code like this, you can set the `requiredSpacesBeforeColon` property to `0`.

```xml
<rule ref="Squiz.ControlStructures.ControlSignature">
    <properties>
        <property name="requiredSpacesBeforeColon" value="0" />
    </properties>
</rule>
```

### Squiz.ControlStructures.ForEachLoopDeclaration

Property Name             | Type | Default | Available Since
------------------------- | ---- | ------- | ---------------
requiredSpacesAfterOpen   | int  | 0       | 1.5.2
requiredSpacesBeforeClose | int  | 0       | 1.5.2

This sniff checks that `foreach` structures have the correct padding inside their bracketed statement. By default, the sniff ensures there are zero spaces following the opening bracket, and zero spaces preceding the closing bracket, as shown in the following code snippet:

```php
foreach ($foo as $bar) {
    // Body.
}
```

Another common way of padding control structures is to use a single space, as shown in the following code snippet:

```php
foreach ( $foo as $bar ) {
    // Body.
}
```

If you prefer to write your code like this, you can set the `requiredSpacesAfterOpen` and `requiredSpacesBeforeClose` properties to `1`, or whatever padding you prefer.

```xml
<rule ref="Squiz.ControlStructures.ForEachLoopDeclaration">
    <properties>
        <property name="requiredSpacesAfterOpen" value="1" />
        <property name="requiredSpacesBeforeClose" value="1" />
    </properties>
</rule>
```

### Squiz.ControlStructures.ForLoopDeclaration

Property Name             | Type | Default | Available Since
------------------------- | ---- | ------- | ---------------
requiredSpacesAfterOpen   | int  | 0       | 1.5.2
requiredSpacesBeforeClose | int  | 0       | 1.5.2

This sniff checks that `for` structures have the correct padding inside their bracketed statement. By default, the sniff ensures there are zero spaces following the opening bracket, and zero spaces preceding the closing bracket, as shown in the following code snippet:

```php
for ($i = 0; $i < 10; $i++) {
    // Body.
}
```

Another common way of padding control structures is to use a single space, as shown in the following code snippet:

```php
for ( $i = 0; $i < 10; $i++ ) {
    // Body.
}
```

If you prefer to write your code like this, you can set the `requiredSpacesAfterOpen` and `requiredSpacesBeforeClose` properties to `1`, or whatever padding you prefer.

```xml
<rule ref="Squiz.ControlStructures.ForLoopDeclaration">
    <properties>
        <property name="requiredSpacesAfterOpen" value="1" />
        <property name="requiredSpacesBeforeClose" value="1" />
    </properties>
</rule>
```

### Squiz.ControlStructures.SwitchDeclaration

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.4.7

Two of the rules that this sniff enforces are the indent of `case` and `default` keywords, and the indent of the case terminating statement. By default, this sniff ensures that the keywords are indented 4 spaces from the `switch` keyword and that the terminating statement is indented 4 spaces from the `case` or `default` keyword, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="Squiz.ControlStructures.SwitchDeclaration">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### Squiz.CSS.ForbiddenStyles

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
error         | bool | true    | 1.4.6

If the `error` property is set to `false`, a warning will be thrown for violations instead of an error.

```xml
<rule ref="Squiz.CSS.ForbiddenStyles">
    <properties>
        <property name="error" value="false" />
    </properties>
</rule>
```

### Squiz.CSS.Indentation

Property Name | Type | Default | Available Since
------------  | ---- | ------- | ---------------
indent        | int  | 4       | 1.4.7

This sniff checks the indentation of CSS class definitions. By default, this sniff ensures that style statements are indented using 4 spaces, but you can change the size of the indent by setting the `indent` property.

```xml
<rule ref="Squiz.CSS.Indentation">
    <properties>
        <property name="indent" value="2" />
    </properties>
</rule>
```

### Squiz.Functions.FunctionDeclaration

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | --------------
ignoreComments | bool | false   | 1.4.0

> Note: The `ignoreComments` property is inherited from the AbstractPattern sniff.

This sniff verifies that functions declarations match a specific pattern of whitespace and bracket placement. By default, comments placed within the function declaration will generate an error, but the sniff can be told to ignore comments by setting the `ignoreComments` property to `true`.

```xml
<rule ref="Squiz.Functions.FunctionDeclaration">
    <properties>
        <property name="ignoreComments" value="false" />
    </properties>
</rule>
```

### Squiz.Functions.FunctionDeclarationArgumentSpacing

Property Name             | Type | Default | Available Since
------------------------- | ---- | ------- | ---------------
equalsSpacing             | int  | 0       | 1.3.5
requiredSpacesAfterOpen   | int  | 0       | 1.5.2
requiredSpacesBeforeClose | int  | 0       | 1.5.2

One of the rules this sniff enforces is the padding around equal signs in the function argument list. By default, the sniff ensures there are zero spaces before and after the equals sign, as shown in the following code snippet:

```php
function foo($a='a', $b='b') {
    // Body.
}
```

Another common way of defining default values is to use a single space, as shown in the following code snippet:

```php
function foo($a = 'a', $b = 'b') {
    // Body.
}
```

If you prefer to write your code like this, you can set the `equalsSpacing` property to `1`, or whatever padding you prefer.

```xml
<rule ref="Squiz.Functions.FunctionDeclarationArgumentSpacing">
    <properties>
        <property name="equalsSpacing" value="1" />
    </properties>
</rule>
```

Another of the rules this sniff enforces is that functions have the correct padding inside their bracketed list of arguments. By default, the sniff ensures there are zero spaces following the opening bracket, and zero spaces preceding the closing bracket, as shown in the following code snippet:

```php
function foo($a, $b) {
    // Body.
}
```

Another common way of padding argument lists is to use a single space, as shown in the following code snippet:

```php
function foo( $a, $b ) {
    // Body.
}
```

If you prefer to write your code like this, you can set the `requiredSpacesAfterOpen` and `requiredSpacesBeforeClose` properties to `1`, or whatever padding you prefer.

```xml
<rule ref="Squiz.Functions.FunctionDeclarationArgumentSpacing">
    <properties>
        <property name="requiredSpacesAfterOpen" value="1" />
        <property name="requiredSpacesBeforeClose" value="1" />
    </properties>
</rule>
```

### Squiz.PHP.CommentedOutCode

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
maxPercentage | int  | 35      | 1.3.3

This sniff generates warnings for commented out code. By default, a warning is generated if a comment appears to be more than 35% valid code. If you find that the sniff is generating a lot of false positive, you may want to raise the valid code threshold by increasing the `maxPercentage` property. Similarly, if you find that the sniff is generating a lot of false negatives, you may want to make it more sensitive by dropping the threshold by decreasing the `maxPercentage` property.

```xml
<!-- Make this sniff more sensitive to commented out code blocks. -->
<rule ref="Squiz.PHP.CommentedOutCode">
    <properties>
        <property name="maxPercentage" value="20" />
    </properties>
</rule>
```

### Squiz.PHP.DiscouragedFunctions

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
error         | bool | false   | -

> Note: This sniff also has a `forbiddenFunctions` property inherited from the [Generic.PHP.ForbiddenFunctions](#genericphpforbiddenfunctions) sniff, but it should not be used. If you want to customise the list of discouraged functions, use the Generic.PHP.ForbiddenFunctions sniff directly.

If the `error` property is set to `true`, an error will be thrown for violations instead of a warning.

```xml
<rule ref="Squiz.PHP.DiscouragedFunctions">
    <properties>
        <property name="error" value="true" />
    </properties>
</rule>
```

### Squiz.PHP.ForbiddenFunctions

Property Name | Type | Default | Available Since
------------- | ---- | ------- | ---------------
error         | bool | false   | -

> Note: This sniff also has a `forbiddenFunctions` property inherited from the [Generic.PHP.ForbiddenFunctions](#genericphpforbiddenfunctions) sniff, but it should not be used. If you want to customise the list of forbidden functions, use the Generic.PHP.ForbiddenFunctions sniff directly.

If the `error` property is set to `true`, an error will be thrown for violations instead of a warning.

```xml
<rule ref="Squiz.PHP.ForbiddenFunctions">
    <properties>
        <property name="error" value="true" />
    </properties>
</rule>
```

### Squiz.Strings.ConcatenationSpacing

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
spacing        | int  | 0       | 2.0.0
ignoreNewlines | bool | false   | 2.3.1

One of the rules this sniff enforces is the padding around concatenation operators. By default, the sniff ensures there are zero spaces before and after the concatenation operator, as shown in the following code snippet:

```php
$foo = $number.'-'.$letter;
```

Another common way of padding concatenation operators is to use a single space, as shown in the following code snippet:

```php
$foo = $number . '-' . $letter;
```

If you prefer to write your code like this, you can set the `spacing` property to `1`, or whatever padding you prefer.

```xml
<rule ref="Squiz.Strings.ConcatenationSpacing">
    <properties>
        <property name="spacing" value="1" />
    </properties>
</rule>
```

Sometimes long concatenation statements are broken over multiple lines to work within a maximum line length, but this sniff will generate an error for these cases by default. Setting the `ignoreNewlines` property to `true` will allow newline characters before or after a concatenation operator, and any required padding for alignment.

```xml
<rule ref="Squiz.Strings.ConcatenationSpacing">
    <properties>
        <property name="ignoreNewlines" value="true" />
    </properties>
</rule>
```

### Squiz.WhiteSpace.FunctionSpacing

Property Name      | Type | Default | Available Since
------------------ | ---- | ------- | ---------------
spacing            | int  | 2       | 1.4.5
spacingBeforeFirst | int  | 2       | 3.3.0
spacingAfterLast   | int  | 2       | 3.3.0

This sniff checks that there are two blank lines before and after functions declarations, but you can change the required padding using the `spacing`, `spacingBeforeFirst`, and `spacingAfterLast` properties.

The `spacingBeforeFirst` property is used to determine how many blank lines are required before a function when it is the first block of code inside a class, interface, or trait. This property is ignored when the function is outside one of these scopes, or if the function is preceded by member vars. If this property has not been set, the sniff will use whatever value has been set for the `spacing` property.

The `spacingAfterLast` property is used to determine how many blank lines are required after a function when it is the last block of code inside a class, interface, or trait. This property is ignored when the function is outside one of these scopes, or if any member vars are placed after the function. If this property has not been set, the sniff will use whatever value has been set for the `spacing` property.

The `spacing` property applies in all other cases.

```xml
<!-- Ensure 1 blank line before and after functions, except at the top and bottom. -->
<rule ref="Squiz.WhiteSpace.FunctionSpacing">
    <properties>
        <property name="spacing" value="1" />
        <property name="spacingBeforeFirst" value="0" />
        <property name="spacingAfterLast" value="0" />
    </properties>
</rule>
```

As the `spacingBeforeFirst` and `spacingAfterLast` properties use the value of the `spacing` property when not set, a shortcut for setting all three properties to the same value is to specify a value for the `spacing` property only.

```xml
<!-- Ensure 1 blank line before and after functions in all cases. -->
<rule ref="Squiz.WhiteSpace.FunctionSpacing">
    <properties>
        <property name="spacing" value="1" />
    </properties>
</rule>
```

### Squiz.WhiteSpace.MemberVarSpacing

Property Name       | Type | Default | Available Since
------------------- | ---- | ------- | ---------------
spacing             | int  | 1       | 3.1.0
spacingBeforeFirst  | int  | 1       | 3.1.0

This sniff checks that there is one blank line before between member vars and before the fist member var, but you can change the required padding using the `spacing` and `spacingBeforeFirst` properties.

```xml
<!--
 Ensure 2 blank lines between member vars,
 but don't require blank lines before the first.
-->
<rule ref="Squiz.WhiteSpace.MemberVarSpacing">
    <properties>
        <property name="spacing" value="2" />
        <property name="spacingBeforeFirst" value="0" />
    </properties>
</rule>
```

### Squiz.WhiteSpace.ObjectOperatorSpacing

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
ignoreNewlines | bool | false   | 2.7.0

This sniff ensures there are no spaces surrounding an object operator. Sometimes long object chains are broken over multiple lines to work within a maximum line length, but this sniff will generate an error for these cases by default. Setting the `ignoreNewlines` property to `true` will allow newline characters before or after an object operator, and any required padding for alignment.

```xml
<rule ref="Squiz.WhiteSpace.ObjectOperatorSpacing">
    <properties>
        <property name="ignoreNewlines" value="true" />
    </properties>
</rule>
```

### Squiz.WhiteSpace.OperatorSpacing

Property Name  | Type | Default | Available Since
-------------- | ---- | ------- | ---------------
ignoreNewlines | bool | false   | 2.2.0

This sniff ensures there is one space before and after an operators. Sometimes long statements are broken over multiple lines to work within a maximum line length, but this sniff will generate an error for these cases by default. Setting the `ignoreNewlines` property to `true` will allow newline characters before or after an operator, and any required padding for alignment.

```xml
<rule ref="Squiz.WhiteSpace.OperatorSpacing">
    <properties>
        <property name="ignoreNewlines" value="true" />
    </properties>
</rule>
```

### Squiz.WhiteSpace.SuperfluousWhitespace

Property Name    | Type | Default | Available Since
---------------- | ---- | ------- | ---------------
ignoreBlankLines | bool | false   | 1.4.2

Some of the rules this sniff enforces are that there should not be whitespace at the end of a line, and that functions should not contain multiple blank lines in a row. If the `ignoreBlankLines` property is set to `true`, blank lines (lines that contain only whitespace) may have spaces and tabs as their content, and multiple blank lines will be allows inside functions.

```xml
<rule ref="Squiz.WhiteSpace.SuperfluousWhitespace">
    <properties>
        <property name="ignoreBlankLines" value="true" />
    </properties>
</rule>
```