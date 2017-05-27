Customisable Sniff Properties
=============================

The behavior of some sniffs can be changed by setting certain sniff
properties in your ruleset.xml file. This page lists the sniff
properties that are available for customisation. For properties that
were added after ruleset support was introduced in version 1.3.0, the
first stable version that made the property available is listed.

For more information about changing sniff behavior by customising your
ruleset, see the [[Annotated ruleset.xml]].

Table of contents
-----------------

-  Generic Sniffs

   -  `Generic.ControlStructures.InlineControlStructure <#genericcontrolstructuresinlinecontrolstructure>`__
   -  `Generic.Debug.ClosureLinter <#genericdebugclosurelinter>`__
   -  `Generic.Files.LineEndings <#genericfileslineendings>`__
   -  `Generic.Files.LineLength <#genericfileslinelength>`__
   -  `Generic.Formatting.MultipleStatementAlignment <#genericformattingmultiplestatementalignment>`__
   -  `Generic.Functions.OpeningFunctionBraceBsdAllman <#genericfunctionsopeningfunctionbracebsdallman>`__
   -  `Generic.Functions.OpeningFunctionBraceKernighanRitchie <#genericfunctionsopeningfunctionbracekernighanritchie>`__
   -  `Generic.Metrics.CyclomaticComplexity <#genericmetricscyclomaticcomplexity>`__
   -  `Generic.Metrics.NestingLevel <#genericmetricsnestinglevel>`__
   -  `Generic.NamingConventions.CamelCapsFunctionName <#genericnamingconventionscamelcapsfunctionname>`__
   -  `Generic.PHP.ForbiddenFunctions <#genericphpforbiddenfunctions>`__
   -  `Generic.PHP.NoSilencedErrors <#genericphpnosilencederrors>`__
   -  `Generic.Strings.UnnecessaryStringConcat <#genericstringsunnecessarystringconcat>`__
   -  `Generic.WhiteSpace.ScopeIndent <#genericwhitespacescopeindent>`__

-  PEAR Sniffs

   -  `PEAR.ControlStructures.ControlSignature <#pearcontrolstructurescontrolsignature>`__
   -  `PEAR.ControlStructures.MultiLineCondition <#pearcontrolstructuresmultilinecondition>`__
   -  `PEAR.Formatting.MultiLineAssignment <#pearformattingmultilineassignment>`__
   -  `PEAR.Functions.FunctionCallSignature <#pearfunctionsfunctioncallsignature>`__
   -  `PEAR.Functions.FunctionDeclaration <#pearfunctionsfunctiondeclaration>`__
   -  `PEAR.WhiteSpace.ObjectOperatorIndent <#pearwhitespaceobjectoperatorindent>`__
   -  `PEAR.WhiteSpace.ScopeClosingBrace <#pearwhitespacescopeclosingbrace>`__
   -  `PEAR.WhiteSpace.ScopeIndent <#pearwhitespacescopeindent>`__

-  PSR2 Sniffs

   -  `PSR2.Classes.ClassDeclaration <#psr2classesclassdeclaration>`__
   -  `PSR2.ControlStructures.ControlStructureSpacing <#psr2controlstructurescontrolstructurespacing>`__
   -  `PSR2.ControlStructures.SwitchDeclaration <#psr2controlstructuresswitchdeclaration>`__

-  Squiz Sniffs

   -  `Squiz.Classes.ClassDeclaration <#squizclassesclassdeclaration>`__
   -  `Squiz.Commenting.LongConditionClosingComment <#squizcommentinglongconditionclosingcomment>`__
   -  `Squiz.ControlStructures.ForEachLoopDeclaration <#squizcontrolstructuresforeachloopdeclaration>`__
   -  `Squiz.ControlStructures.ForLoopDeclaration <#squizcontrolstructuresforloopdeclaration>`__
   -  `Squiz.ControlStructures.SwitchDeclaration <#squizcontrolstructuresswitchdeclaration>`__
   -  `Squiz.CSS.ForbiddenStyles <#squizcssforbiddenstyles>`__
   -  `Squiz.CSS.Indentation <#squizcssindentation>`__
   -  `Squiz.Functions.FunctionDeclaration <#squizfunctionsfunctiondeclaration>`__
   -  `Squiz.Functions.FunctionDeclarationArgumentSpacing <#squizfunctionsfunctiondeclarationargumentspacing>`__
   -  `Squiz.PHP.CommentedOutCode <#squizphpcommentedoutcode>`__
   -  `Squiz.PHP.DiscouragedFunctions <#squizphpdiscouragedfunctions>`__
   -  `Squiz.PHP.ForbiddenFunctions <#squizphpforbiddenfunctions>`__
   -  `Squiz.Strings.ConcatenationSpacing <#squizstringsconcatenationspacing>`__
   -  `Squiz.WhiteSpace.FunctionSpacing <#squizwhitespacefunctionspacing>`__
   -  `Squiz.WhiteSpace.ObjectOperatorSpacing <#squizwhitespaceobjectoperatorspacing>`__
   -  `Squiz.WhiteSpace.OperatorSpacing <#squizwhitespaceoperatorspacing>`__
   -  `Squiz.WhiteSpace.SuperfluousWhitespace <#squizwhitespacesuperfluouswhitespace>`__

--------------

Generic Sniffs
--------------

Generic.ControlStructures.InlineControlStructure
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| error           | bool   | true      | -                 |
+-----------------+--------+-----------+-------------------+

If the ``error`` property is set to ``false``, a warning will be thrown
for violations instead of an error.

.. code:: xml

    <rule ref="Generic.ControlStructures.InlineControlStructure">
        <properties>
            <property name="error" value="false" />
        </properties>
    </rule>

Generic.Debug.ClosureLinter
~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+---------+-----------+-------------------+
| Property Name   | Type    | Default   | Available Since   |
+=================+=========+===========+===================+
| errorCodes      | array   | -         | -                 |
+-----------------+---------+-----------+-------------------+
| ignoreCodes     | array   | -         | -                 |
+-----------------+---------+-----------+-------------------+

The ``Generic.Debug.ClosureLinter`` sniff runs the `Google Closure
Linter <https://github.com/google/closure-linter>`__ tool over
JavaScript files and reports errors that the tool finds. All found
errors are reported as PHP\_CodeSniffer warnings by default.

There are two configurable options:

-  ``errorCodes`` : a list of error codes that should show as errors
   instead of warnings
-  ``ignoreCodes`` : a list of error codes that should be ignored

    Note: The error codes accepted by this sniff are the 4-digit codes
    generated by the ``gjslint`` tool and displayed in the warning
    messages produced by this sniff.

.. code:: xml

    <rule ref="Generic.Debug.ClosureLinter">
        <properties>
            <property name="errorCodes" type="array" value="0210"/>
            <property name="ignoreCodes" type="array" value="0001,0110,0240"/>
        </properties>
    </rule>

Generic.Files.LineEndings
~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+----------+-----------+-------------------+
| Property Name   | Type     | Default   | Available Since   |
+=================+==========+===========+===================+
| eolChar         | string   | \\n       | -                 |
+-----------------+----------+-----------+-------------------+

This sniff ensures that files use a specific line ending, which can be
customised by setting the ``eolChar`` property.

.. code:: xml

    <rule ref="Generic.Files.LineEndings">
        <properties>
            <property name="eolChar" value="\r\n" />
        </properties>
    </rule>

Generic.Files.LineLength
~~~~~~~~~~~~~~~~~~~~~~~~

+---------------------+--------+-----------+-------------------+
| Property Name       | Type   | Default   | Available Since   |
+=====================+========+===========+===================+
| lineLimit           | int    | 80        | -                 |
+---------------------+--------+-----------+-------------------+
| absoluteLineLimit   | int    | 100       | -                 |
+---------------------+--------+-----------+-------------------+

This sniff checks all lines in a file and generates warnings if they are
over ``lineLimit`` characters in length and errors if they are over
``absoluteLineLimit`` in length. These properties can be used to set the
threshold at which errors are reported.

    Note: The value of the ``lineLimit`` property should be less than or
    equal to the value of the ``absoluteLineLimit`` property.

.. code:: xml

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

If errors are not required, the value of ``absoluteLineLimit`` can be
set to zero.

.. code:: xml

    <!-- Warn about lines longer than 135 chars, and never error. -->
    <rule ref="Generic.Files.LineLength">
        <properties>
            <property name="lineLimit" value="135" />
            <property name="absoluteLineLimit" value="0" />
        </properties>
    </rule>

Generic.Formatting.MultipleStatementAlignment
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| maxPadding      | int    | 1000      | -                 |
+-----------------+--------+-----------+-------------------+
| error           | bool   | false     | -                 |
+-----------------+--------+-----------+-------------------+

This sniff checks the alignment of assignment operators. If there are
multiple adjacent assignments, it checks that the equals signs of each
assignment are aligned.

The difference in alignment between two adjacent assignments is
occasionally quite large, so aligning equals signs would create
extremely long lines. By setting the ``maxPadding`` property, you can
configure the maximum amount of padding required to align the assignment
with the surrounding assignments before the alignment is ignored and no
warnings will be generated.

.. code:: xml

    <rule ref="Generic.Formatting.MultipleStatementAlignment">
        <properties>
            <property name="maxPadding" value="50" />
        </properties>
    </rule>

If the ``error`` property is set to ``true``, an error will be thrown
for violations instead of a warning.

.. code:: xml

    <rule ref="Generic.Formatting.MultipleStatementAlignment">
        <properties>
            <property name="error" value="true" />
        </properties>
    </rule>

Generic.Functions.OpeningFunctionBraceBsdAllman
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| checkFunctions   | bool   | true      | 2.3.0             |
+------------------+--------+-----------+-------------------+
| checkClosures    | bool   | false     | 2.3.0             |
+------------------+--------+-----------+-------------------+

The sniff checks the position of the opening brace of a function and/or
closure (anonymous function). The sniff only checks functions by
default, but the ``checkFunctions`` and ``checkClosures`` properties can
be used to have the sniff check one or both of these code blocks.

.. code:: xml

    <!-- Don't check function braces, but check closure braces. -->
    <rule ref="Generic.Functions.OpeningFunctionBraceBsdAllman">
        <properties>
            <property name="checkFunctions" value="false" />
            <property name="checkClosures" value="true" />
        </properties>
    </rule>

Generic.Functions.OpeningFunctionBraceKernighanRitchie
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| checkFunctions   | bool   | true      | 2.3.0             |
+------------------+--------+-----------+-------------------+
| checkClosures    | bool   | false     | 2.3.0             |
+------------------+--------+-----------+-------------------+

The sniff checks the position of the opening brace of a function and/or
closure (anonymous function). The sniff only checks functions by
default, but the ``checkFunctions`` and ``checkClosures`` properties can
be used to have the sniff check one or both of these code blocks.

.. code:: xml

    <!-- Don't check function braces, but check closure braces. -->
    <rule ref="Generic.Functions.OpeningFunctionBraceKernighanRitchie">
        <properties>
            <property name="checkFunctions" value="false" />
            <property name="checkClosures" value="true" />
        </properties>
    </rule>

Generic.Metrics.CyclomaticComplexity
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+----------------------+--------+-----------+-------------------+
| Property Name        | Type   | Default   | Available Since   |
+======================+========+===========+===================+
| complexity           | int    | 10        | -                 |
+----------------------+--------+-----------+-------------------+
| absoluteComplexity   | int    | 20        | -                 |
+----------------------+--------+-----------+-------------------+

This sniff checks the cyclomatic complexity for functions by counting
the different paths the function includes.

There are two configurable options:

-  ``complexity`` : the cyclomatic complexity above which this sniff
   will generate warnings
-  ``absoluteComplexity`` : the cyclomatic complexity above which this
   sniff will generate errors

    Note: The value of the ``complexity`` property should be less than
    or equal to the value of the ``absoluteComplexity`` property.

.. code:: xml

    <rule ref="Generic.Metrics.CyclomaticComplexity">
        <properties>
            <property name="complexity" value="15" />
            <property name="absoluteComplexity" value="30" />
        </properties>
    </rule>

Generic.Metrics.NestingLevel
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------------+--------+-----------+-------------------+
| Property Name          | Type   | Default   | Available Since   |
+========================+========+===========+===================+
| nestingLevel           | int    | 5         | -                 |
+------------------------+--------+-----------+-------------------+
| absoluteNestingLevel   | int    | 10        | -                 |
+------------------------+--------+-----------+-------------------+

This sniff checks how many level deep that code is nested within a
function.

There are two configurable options:

-  ``nestingLevel`` : the nesting level above which this sniff will
   generate warnings
-  ``absoluteNestingLevel`` : the nesting level above which this sniff
   will generate errors

.. code:: xml

    <rule ref="Generic.Metrics.NestingLevel">
        <properties>
            <property name="nestingLevel" value="8" />
            <property name="absoluteNestingLevel" value="12" />
        </properties>
    </rule>

Generic.NamingConventions.CamelCapsFunctionName
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| strict          | bool   | true      | 1.3.5             |
+-----------------+--------+-----------+-------------------+

This sniff ensures function and method names are in CamelCaps.

Strictly speaking, a name cannot have two capital letters next to each
other in CamelCaps format. By setting the ``strict`` property to
``false``, the sniff applies the rule more leniently and allows for two
capital letters next to each other in function and method names.

.. code:: xml

    <rule ref="Generic.NamingConventions.CamelCapsFunctionName">
        <properties>
            <property name="strict" value="false" />
        </properties>
    </rule>

Generic.PHP.ForbiddenFunctions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+----------------------+---------+-------------------------------+-------------------+
| Property Name        | Type    | Default                       | Available Since   |
+======================+=========+===============================+===================+
| forbiddenFunctions   | array   | sizeof=>count,delete=>unset   | 2.0.0             |
+----------------------+---------+-------------------------------+-------------------+
| error                | bool    | true                          | -                 |
+----------------------+---------+-------------------------------+-------------------+

This sniff discourages the use of alias functions that are kept in PHP
for compatibility with older versions. The sniff can be used to forbid
the use of any function by setting the ``forbiddenFunctions`` property.
The property is defined as an array, with the keys being the names of
the functions to forbid and the values being the names of suggested
alternative functions to use instead. If no alternative function exists
(i.e., the function should never be used) specify ``null`` as the value.

.. code:: xml

    <rule ref="Generic.PHP.ForbiddenFunctions">
        <properties>
            <property name="forbiddenFunctions" type="array"
                value="print=>echo,create_function=>null" />
         </properties>
    </rule>

If the ``error`` property is set to ``false``, a warning will be thrown
for violations instead of an error.

.. code:: xml

    <rule ref="Generic.PHP.ForbiddenFunctions">
        <properties>
            <property name="error" value="false" />
        </properties>
    </rule>

Generic.PHP.NoSilencedErrors
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| error           | bool   | true      | -                 |
+-----------------+--------+-----------+-------------------+

If the ``error`` property is set to ``false``, a warning will be thrown
for violations instead of an error.

.. code:: xml

    <rule ref="Generic.PHP.NoSilencedErrors">
        <properties>
            <property name="error" value="false" />
        </properties>
    </rule>

Generic.Strings.UnnecessaryStringConcat
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| allowMultiline   | bool   | false     | 2.3.4             |
+------------------+--------+-----------+-------------------+
| error            | bool   | true      | -                 |
+------------------+--------+-----------+-------------------+

This sniff checks that two strings using the same quoting style are not
concatenated. Sometimes long strings are broken over multiple lines to
work within a maximum line length, but this sniff will generate an error
for these cases by default. Setting the ``allowMultiline`` property to
``true`` will get the sniff to allow string concatenation if the string
covers multiple lines.

.. code:: xml

    <rule ref="Generic.Strings.UnnecessaryStringConcat">
        <properties>
            <property name="allowMultiline" value="true" />
        </properties>
    </rule>

If the ``error`` property is set to ``false``, a warning will be thrown
for violations instead of an error.

.. code:: xml

    <rule ref="Generic.Strings.UnnecessaryStringConcat">
        <properties>
            <property name="error" value="false" />
        </properties>
    </rule>

Generic.WhiteSpace.ScopeIndent
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+---------------------------+---------+-----------+-------------------+
| Property Name             | Type    | Default   | Available Since   |
+===========================+=========+===========+===================+
| indent                    | int     | 4         | -                 |
+---------------------------+---------+-----------+-------------------+
| exact                     | bool    | false     | -                 |
+---------------------------+---------+-----------+-------------------+
| tabIndent                 | bool    | false     | 2.0.0             |
+---------------------------+---------+-----------+-------------------+
| ignoreIndentationTokens   | array   | -         | 1.4.8             |
+---------------------------+---------+-----------+-------------------+

This sniff checks that code blocks are indented correctly. By default,
this sniff ensures that code blocks are indented 4 spaces, but you can
change the size of the indent by setting the ``indent`` property.

.. code:: xml

    <rule ref="Generic.WhiteSpace.ScopeIndent">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

The ``exact`` property is used to determine whether an indent is treated
as an exact number or as a minimum amount. By default, code blocks must
be indented at least ``indent`` spaces from the last code block. If
``exact`` is set to ``true``, code blocks must be indented exactly
``indent`` spaces from the last code block.

    Note: Enforcing exact indent checking is generally not advised
    because it doesn't allow for any flexibility when indenting and
    aligning code. It is almost always better to use the default value
    and then allow other sniffs to enforce specific indenting rules.

.. code:: xml

    <rule ref="Generic.WhiteSpace.ScopeIndent">
        <properties>
            <property name="exact" value="true" />
        </properties>
    </rule>

By default, this sniff enforces the use of spaces for indentation and
also uses spaces when fixing the indentation of code blocks. If you
prefer using tabs, you can set the ``tabIndent`` property to ``true``.

    Note: The size of each tab is important, so it should be specified
    using the ``--tab-width`` CLI argument or by adding
    ``<arg name="tab-width" value="4"/>`` to your ruleset. This sniff
    will use this value when checking and fixing indents.

.. code:: xml

    <!-- Tabs should represent 4 spaces. -->
    <arg name="tab-width" value="4"/>
    ...
    <!-- Indent using tabs. -->
    <rule ref="Generic.WhiteSpace.ScopeIndent">
        <properties>
            <property name="tabIndent" value="true" />
        </properties>
    </rule>

Setting the ``ignoreIndentationTokens`` property provides the sniff with
a list of tokens that do not need to be checked for indentation. This is
commonly used to ignore indentation for code structures such as comments
and here/nowdocs.

.. code:: xml

    <rule ref="Generic.WhiteSpace.ScopeIndent">
        <properties>
            <property name="ignoreIndentationTokens" type="array"
                value="T_COMMENT,T_DOC_COMMENT_OPEN_TAG"/>
        </properties>
    </rule>

PEAR Sniffs
-----------

PEAR.ControlStructures.ControlSignature
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| ignoreComments   | bool   | true      | 1.4.0             |
+------------------+--------+-----------+-------------------+

    Note: The ``ignoreComments`` property is inherited from the
    AbstractPattern sniff.

This sniff verifies that control structures match a specific pattern of
whitespace and bracket placement. By default, comments placed within the
declaration will generate an error, but the sniff can be told to ignore
comments by setting the ``ignoreComments`` property to ``true``.

.. code:: xml

    <rule ref="PEAR.ControlStructures.ControlSignature">
        <properties>
            <property name="ignoreComments" value="false" />
        </properties>
    </rule>

PEAR.ControlStructures.MultiLineCondition
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.4.7             |
+-----------------+--------+-----------+-------------------+

One of the rules that this sniff enforces is the indent of a condition
that has been split over multiple lines. By default, this sniff ensures
that each line of the condition is indented 4 spaces, but you can change
the size of the indent by setting the ``indent`` property.

.. code:: xml

    <rule ref="PEAR.ControlStructures.MultiLineCondition">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

PEAR.Formatting.MultiLineAssignment
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.4.7             |
+-----------------+--------+-----------+-------------------+

One of the rules that this sniff enforces is the indent of an assignment
that has been split over multiple lines. By default, this sniff ensures
that the line with the assignment operator is indented 4 spaces, but you
can change the size of the indent by setting the ``indent`` property.

.. code:: xml

    <rule ref="PEAR.Formatting.MultiLineAssignment">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

PEAR.Functions.FunctionCallSignature
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------------------+--------+-----------+-------------------+
| Property Name               | Type   | Default   | Available Since   |
+=============================+========+===========+===================+
| indent                      | int    | 4         | 1.3.4             |
+-----------------------------+--------+-----------+-------------------+
| allowMultipleArguments      | bool   | true      | 1.3.6             |
+-----------------------------+--------+-----------+-------------------+
| requiredSpacesAfterOpen     | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+
| requiredSpacesBeforeClose   | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+

One of the rules this sniff enforces is that function calls have the
correct padding inside their bracketed argument lists. By default, the
sniff ensures there are zero spaces following the opening bracket, and
zero spaces preceding the closing bracket, as shown in the following
code snippet:

.. code:: php

    $foo = getValue($a, $b, $c);

Another common way of padding function calls is to use a single space,
as shown in the following code snippet:

.. code:: php

    $foo = getValue( $a, $b, $c );

If you prefer to write your code like this, you can set the
``requiredSpacesAfterOpen`` and ``requiredSpacesBeforeClose`` properties
to ``1``, or whatever padding you prefer.

.. code:: xml

    <rule ref="PEAR.Functions.FunctionCallSignature">
        <properties>
            <property name="requiredSpacesAfterOpen" value="1" />
            <property name="requiredSpacesBeforeClose" value="1" />
        </properties>
    </rule>

This sniff also enforces the formatting of multi-line function calls. By
default, multiple arguments can appear on each line, as shown in the
following code snippet:

.. code:: php

    $returnValue = foo(
        $a, $b, $c,
        $d, $e
    );

Another common way of defining multi-line function calls is to have one
argument per line, as shown in the following code snippet:

.. code:: php

    $returnValue = foo(
        $a,
        $b,
        $c,
        $d,
        $e
    );

If you prefer to write your code like this, you can set the
``allowMultipleArguments`` property to ``false``.

.. code:: xml

    <rule ref="PEAR.Functions.FunctionCallSignature">
        <properties>
            <property name="allowMultipleArguments" value="false" />
        </properties>
    </rule>

By default, this sniff ensures that each line in a multi-line function
call is indented 4 spaces, but you can change the size of the indent by
setting the ``indent`` property.

.. code:: xml

    <rule ref="PEAR.Functions.FunctionCallSignature">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

PEAR.Functions.FunctionDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.4.7             |
+-----------------+--------+-----------+-------------------+

One of the rules that this sniff enforces is the indent of each function
argument in a multi-line function declaration. By default, this sniff
ensures that each line is indented 4 spaces, but you can change the size
of the indent by setting the ``indent`` property.

.. code:: xml

    <rule ref="PEAR.Functions.FunctionDeclaration">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

PEAR.WhiteSpace.ObjectOperatorIndent
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.4.6             |
+-----------------+--------+-----------+-------------------+

One of the rules that this sniff enforces is the indent of each line in
a multi-line object chain. By default, this sniff ensures that each line
is indented 4 spaces, but you can change the size of the indent by
setting the ``indent`` property.

.. code:: xml

    <rule ref="PEAR.WhiteSpace.ObjectOperatorIndent">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

PEAR.WhiteSpace.ScopeClosingBrace
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.3.4             |
+-----------------+--------+-----------+-------------------+

One of the rules that this sniff enforces is the indent of the case
terminating statement. By default, this sniff ensures that the statement
is indented 4 spaces from the ``case`` or ``default`` keyword, but you
can change the size of the indent by setting the ``indent`` property.

.. code:: xml

    <rule ref="PEAR.WhiteSpace.ScopeClosingBrace">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

PEAR.WhiteSpace.ScopeIndent
~~~~~~~~~~~~~~~~~~~~~~~~~~~

+---------------------------+---------+-----------+-------------------+
| Property Name             | Type    | Default   | Available Since   |
+===========================+=========+===========+===================+
| indent                    | int     | 4         | -                 |
+---------------------------+---------+-----------+-------------------+
| exact                     | bool    | false     | -                 |
+---------------------------+---------+-----------+-------------------+
| tabIndent                 | bool    | false     | 2.0.0             |
+---------------------------+---------+-----------+-------------------+
| ignoreIndentationTokens   | array   | -         | 1.4.8             |
+---------------------------+---------+-----------+-------------------+

    Note: All properties are inherited from the
    [Generic.WhiteSpace.ScopeIndent] (#genericwhitespacescopeindent)
    sniff.

See the [Generic.WhiteSpace.ScopeIndent] (#genericwhitespacescopeindent)
sniff for an explanation of all properties.

.. code:: xml

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

PSR2 Sniffs
-----------

PSR2.Classes.ClassDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.3.5             |
+-----------------+--------+-----------+-------------------+

One of the rules that this sniff enforces is the indent of a list of
implemented or extended class names that have been split over multiple
lines. By default, this sniff ensures that the class names are indented
4 spaces, but you can change the size of the indent by setting the
``indent`` property.

.. code:: xml

    <rule ref="PSR2.Classes.ClassDeclaration">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

PSR2.ControlStructures.ControlStructureSpacing
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------------------+--------+-----------+-------------------+
| Property Name               | Type   | Default   | Available Since   |
+=============================+========+===========+===================+
| requiredSpacesAfterOpen     | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+
| requiredSpacesBeforeClose   | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+

This sniff checks that control structures have the correct padding
inside their bracketed statement. By default, the sniff ensures there
are zero spaces following the opening bracket, and zero spaces preceding
the closing bracket, as shown in the following code snippet:

.. code:: php

    if ($condition === true) {
        // Body.
    }

Another common way of padding control structures is to use a single
space, as shown in the following code snippet:

.. code:: php

    if ( $condition === true ) {
        // Body.
    }

If you prefer to write your code like this, you can set the
``requiredSpacesAfterOpen`` and ``requiredSpacesBeforeClose`` properties
to ``1``, or whatever padding you prefer.

.. code:: xml

    <rule ref="PSR2.ControlStructures.ControlStructureSpacing">
        <properties>
            <property name="requiredSpacesAfterOpen" value="1" />
            <property name="requiredSpacesBeforeClose" value="1" />
        </properties>
    </rule>

PSR2.ControlStructures.SwitchDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.4.5             |
+-----------------+--------+-----------+-------------------+

One of the rules that this sniff enforces is the indent of the case
terminating statement. By default, this sniff ensures that the statement
is indented 4 spaces from the ``case`` or ``default`` keyword, but you
can change the size of the indent by setting the ``indent`` property.

.. code:: xml

    <rule ref="PSR2.ControlStructures.SwitchDeclaration">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

Squiz Sniffs
------------

Squiz.Classes.ClassDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.3.5             |
+-----------------+--------+-----------+-------------------+

    Note: The ``indent`` property is inherited from the
    [PSR2.Classes.ClassDeclaration] (#psr2classesclassdeclaration)
    sniff.

One of the rules that this sniff enforces is the indent of a list of
implemented or extended class names that have been split over multiple
lines. By default, this sniff ensures that the class names are indented
4 spaces, but you can change the size of the indent by setting the
``indent`` property.

.. code:: xml

    <rule ref="Squiz.Classes.ClassDeclaration">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

Squiz.Commenting.LongConditionClosingComment
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+----------+------------+-------------------+
| Property Name   | Type     | Default    | Available Since   |
+=================+==========+============+===================+
| lineLimit       | int      | 20         | 2.7.0             |
+-----------------+----------+------------+-------------------+
| commentFormat   | string   | //end %s   | 2.7.0             |
+-----------------+----------+------------+-------------------+

This sniff checks that long blocks of code have a closing comment. The
``lineLimit`` property allows you to configure the numbers of lines that
the code block must span before requiring a comment. By default, the
code block must be at least 20 lines long, including the opening and
closing lines, but you can change the required length by setting the
``lineLimit`` property.

.. code:: xml

    <rule ref="Squiz.Commenting.LongConditionClosingComment">
        <properties>
            <property name="lineLimit" value="40" />
        </properties>
    </rule>

When a closing comment is required, the format defaults to ``//end %s``,
where the %s placeholder is replaced with the type of the code block.
For example, ``//end if``, ``//end foreach``, or ``//end switch``. You
can change the format of the end comment by setting the
``commentFormat`` property.

.. code:: xml

    <!-- Have code block comments look like // end foreach() etc. -->
    <rule ref="Squiz.Commenting.LongConditionClosingComment">
        <properties>
            <property name="commentFormat" value="// end %s()" />
        </properties>
    </rule>

Squiz.ControlStructures.ForEachLoopDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------------------+--------+-----------+-------------------+
| Property Name               | Type   | Default   | Available Since   |
+=============================+========+===========+===================+
| requiredSpacesAfterOpen     | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+
| requiredSpacesBeforeClose   | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+

This sniff checks that ``foreach`` structures have the correct padding
inside their bracketed statement. By default, the sniff ensures there
are zero spaces following the opening bracket, and zero spaces preceding
the closing bracket, as shown in the following code snippet:

.. code:: php

    foreach ($foo as $bar) {
        // Body.
    }

Another common way of padding control structures is to use a single
space, as shown in the following code snippet:

.. code:: php

    foreach ( $foo as $bar ) {
        // Body.
    }

If you prefer to write your code like this, you can set the
``requiredSpacesAfterOpen`` and ``requiredSpacesBeforeClose`` properties
to ``1``, or whatever padding you prefer.

.. code:: xml

    <rule ref="Squiz.ControlStructures.ForEachLoopDeclaration">
        <properties>
            <property name="requiredSpacesAfterOpen" value="1" />
            <property name="requiredSpacesBeforeClose" value="1" />
        </properties>
    </rule>

Squiz.ControlStructures.ForLoopDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------------------+--------+-----------+-------------------+
| Property Name               | Type   | Default   | Available Since   |
+=============================+========+===========+===================+
| requiredSpacesAfterOpen     | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+
| requiredSpacesBeforeClose   | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+

This sniff checks that ``for`` structures have the correct padding
inside their bracketed statement. By default, the sniff ensures there
are zero spaces following the opening bracket, and zero spaces preceding
the closing bracket, as shown in the following code snippet:

.. code:: php

    for ($i = 0; $i < 10; $i++) {
        // Body.
    }

Another common way of padding control structures is to use a single
space, as shown in the following code snippet:

.. code:: php

    for ( $i = 0; $i < 10; $i++ ) {
        // Body.
    }

If you prefer to write your code like this, you can set the
``requiredSpacesAfterOpen`` and ``requiredSpacesBeforeClose`` properties
to ``1``, or whatever padding you prefer.

.. code:: xml

    <rule ref="Squiz.ControlStructures.ForLoopDeclaration">
        <properties>
            <property name="requiredSpacesAfterOpen" value="1" />
            <property name="requiredSpacesBeforeClose" value="1" />
        </properties>
    </rule>

Squiz.ControlStructures.SwitchDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.4.7             |
+-----------------+--------+-----------+-------------------+

Two of the rules that this sniff enforces are the indent of ``case`` and
``default`` keywords, and the indent of the case terminating statement.
By default, this sniff ensures that the keywords are indented 4 spaces
from the ``switch`` keyword and that the terminating statement is
indented 4 spaces from the ``case`` or ``default`` keyword, but you can
change the size of the indent by setting the ``indent`` property.

.. code:: xml

    <rule ref="Squiz.ControlStructures.SwitchDeclaration">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

Squiz.CSS.ForbiddenStyles
~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| error           | bool   | true      | 1.4.6             |
+-----------------+--------+-----------+-------------------+

If the ``error`` property is set to ``false``, a warning will be thrown
for violations instead of an error.

.. code:: xml

    <rule ref="Squiz.CSS.ForbiddenStyles">
        <properties>
            <property name="error" value="false" />
        </properties>
    </rule>

Squiz.CSS.Indentation
~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| indent          | int    | 4         | 1.4.7             |
+-----------------+--------+-----------+-------------------+

This sniff checks the indentation of CSS class definitions. By default,
this sniff ensures that style statements are indented using 4 spaces,
but you can change the size of the indent by setting the ``indent``
property.

.. code:: xml

    <rule ref="Squiz.CSS.Indentation">
        <properties>
            <property name="indent" value="2" />
        </properties>
    </rule>

Squiz.Functions.FunctionDeclaration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| ignoreComments   | bool   | false     | 1.4.0             |
+------------------+--------+-----------+-------------------+

    Note: The ``ignoreComments`` property is inherited from the
    AbstractPattern sniff.

This sniff verifies that functions declarations match a specific pattern
of whitespace and bracket placement. By default, comments placed within
the function declaration will generate an error, but the sniff can be
told to ignore comments by setting the ``ignoreComments`` property to
``true``.

.. code:: xml

    <rule ref="Squiz.Functions.FunctionDeclaration">
        <properties>
            <property name="ignoreComments" value="false" />
        </properties>
    </rule>

Squiz.Functions.FunctionDeclarationArgumentSpacing
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------------------+--------+-----------+-------------------+
| Property Name               | Type   | Default   | Available Since   |
+=============================+========+===========+===================+
| equalsSpacing               | int    | 0         | 1.3.5             |
+-----------------------------+--------+-----------+-------------------+
| requiredSpacesAfterOpen     | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+
| requiredSpacesBeforeClose   | int    | 0         | 1.5.2             |
+-----------------------------+--------+-----------+-------------------+

One of the rules this sniff enforces is the padding around equal signs
in the function argument list. By default, the sniff ensures there are
zero spaces before and after the equals sign, as shown in the following
code snippet:

.. code:: php

    function foo($a='a', $b='b') {
        // Body.
    }

Another common way of defining default values is to use a single space,
as shown in the following code snippet:

.. code:: php

    function foo($a = 'a', $b = 'b') {
        // Body.
    }

If you prefer to write your code like this, you can set the
``equalsSpacing`` property to ``1``, or whatever padding you prefer.

.. code:: xml

    <rule ref="Squiz.Functions.FunctionDeclarationArgumentSpacing">
        <properties>
            <property name="equalsSpacing" value="1" />
        </properties>
    </rule>

Another of the rules this sniff enforces is that functions have the
correct padding inside their bracketed list of arguments. By default,
the sniff ensures there are zero spaces following the opening bracket,
and zero spaces preceding the closing bracket, as shown in the following
code snippet:

.. code:: php

    function foo($a, $b) {
        // Body.
    }

Another common way of padding argument lists is to use a single space,
as shown in the following code snippet:

.. code:: php

    function foo( $a, $b ) {
        // Body.
    }

If you prefer to write your code like this, you can set the
``requiredSpacesAfterOpen`` and ``requiredSpacesBeforeClose`` properties
to ``1``, or whatever padding you prefer.

.. code:: xml

    <rule ref="Squiz.Functions.FunctionDeclarationArgumentSpacing">
        <properties>
            <property name="requiredSpacesAfterOpen" value="1" />
            <property name="requiredSpacesBeforeClose" value="1" />
        </properties>
    </rule>

Squiz.PHP.CommentedOutCode
~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| maxPercentage   | int    | 35        | 1.3.3             |
+-----------------+--------+-----------+-------------------+

This sniff generates warnings for commented out code. By default, a
warning is generated if a comment appears to be more than 35% valid
code. If you find that the sniff is generating a lot of false positive,
you may want to raise the valid code threshold by increasing the
``maxPercentage`` property. Similarly, if you find that the sniff is
generating a lot of false negatives, you may want to make it more
sensitive by dropping the threshold by decreasing the ``maxPercentage``
property.

.. code:: xml

    <!-- Make this sniff more sensitive to commented out code blocks. -->
    <rule ref="Squiz.PHP.CommentedOutCode">
        <properties>
            <property name="maxPercentage" value="20" />
        </properties>
    </rule>

Squiz.PHP.DiscouragedFunctions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| error           | bool   | false     | -                 |
+-----------------+--------+-----------+-------------------+

    Note: This sniff also has a ``forbiddenFunctions`` property
    inherited from the [Generic.PHP.ForbiddenFunctions]
    (#genericphpforbiddenfunctions) sniff, but it should not be used. If
    you want to customise the list of discouraged functions, use the
    Generic.PHP.ForbiddenFunctions sniff directly.

If the ``error`` property is set to ``true``, an error will be thrown
for violations instead of a warning.

.. code:: xml

    <rule ref="Squiz.PHP.DiscouragedFunctions">
        <properties>
            <property name="error" value="true" />
        </properties>
    </rule>

Squiz.PHP.ForbiddenFunctions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| error           | bool   | false     | -                 |
+-----------------+--------+-----------+-------------------+

    Note: This sniff also has a ``forbiddenFunctions`` property
    inherited from the [Generic.PHP.ForbiddenFunctions]
    (#genericphpforbiddenfunctions) sniff, but it should not be used. If
    you want to customise the list of forbidden functions, use the
    Generic.PHP.ForbiddenFunctions sniff directly.

If the ``error`` property is set to ``true``, an error will be thrown
for violations instead of a warning.

.. code:: xml

    <rule ref="Squiz.PHP.ForbiddenFunctions">
        <properties>
            <property name="error" value="true" />
        </properties>
    </rule>

Squiz.Strings.ConcatenationSpacing
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| spacing          | int    | 0         | 2.0.0             |
+------------------+--------+-----------+-------------------+
| ignoreNewlines   | bool   | false     | 2.3.1             |
+------------------+--------+-----------+-------------------+

One of the rules this sniff enforces is the padding around concatenation
operators. By default, the sniff ensures there are zero spaces before
and after the concatenation operator, as shown in the following code
snippet:

.. code:: php

    $foo = $number.'-'.$letter;

Another common way of padding concatenation operators is to use a single
space, as shown in the following code snippet:

.. code:: php

    $foo = $number . '-' . $letter;

If you prefer to write your code like this, you can set the ``spacing``
property to ``1``, or whatever padding you prefer.

.. code:: xml

    <rule ref="Squiz.Strings.ConcatenationSpacing">
        <properties>
            <property name="spacing" value="1" />
        </properties>
    </rule>

Sometimes long concatenation statements are broken over multiple lines
to work within a maximum line length, but this sniff will generate an
error for these cases by default. Setting the ``ignoreNewlines``
property to ``true`` will allow newline characters before or after a
concatenation operator, and any required padding for alignment.

.. code:: xml

    <rule ref="Squiz.Strings.ConcatenationSpacing">
        <properties>
            <property name="ignoreNewlines" value="true" />
        </properties>
    </rule>

Squiz.WhiteSpace.FunctionSpacing
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+-----------------+--------+-----------+-------------------+
| Property Name   | Type   | Default   | Available Since   |
+=================+========+===========+===================+
| spacing         | int    | 2         | 1.4.5             |
+-----------------+--------+-----------+-------------------+

This sniff checks that there are two blank lines before and after
functions declarations, but you can change the required padding using
the ``spacing`` property.

.. code:: xml

    <!-- Ensure 1 blank line before and after functions. -->
    <rule ref="Squiz.WhiteSpace.FunctionSpacing">
        <properties>
            <property name="spacing" value="1" />
        </properties>
    </rule>

Squiz.WhiteSpace.ObjectOperatorSpacing
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| ignoreNewlines   | bool   | false     | 2.7.0             |
+------------------+--------+-----------+-------------------+

This sniff ensures there are no spaces surrounding an object operator.
Sometimes long object chains are broken over multiple lines to work
within a maximum line length, but this sniff will generate an error for
these cases by default. Setting the ``ignoreNewlines`` property to
``true`` will allow newline characters before or after an object
operator, and any required padding for alignment.

.. code:: xml

    <rule ref="Squiz.WhiteSpace.ObjectOperatorSpacing">
        <properties>
            <property name="ignoreNewlines" value="true" />
        </properties>
    </rule>

Squiz.WhiteSpace.OperatorSpacing
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+------------------+--------+-----------+-------------------+
| Property Name    | Type   | Default   | Available Since   |
+==================+========+===========+===================+
| ignoreNewlines   | bool   | false     | 2.2.0             |
+------------------+--------+-----------+-------------------+

This sniff ensures there is one space before and after an operators.
Sometimes long statements are broken over multiple lines to work within
a maximum line length, but this sniff will generate an error for these
cases by default. Setting the ``ignoreNewlines`` property to ``true``
will allow newline characters before or after an operator, and any
required padding for alignment.

.. code:: xml

    <rule ref="Squiz.WhiteSpace.OperatorSpacing">
        <properties>
            <property name="ignoreNewlines" value="true" />
        </properties>
    </rule>

Squiz.WhiteSpace.SuperfluousWhitespace
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

+--------------------+--------+-----------+-------------------+
| Property Name      | Type   | Default   | Available Since   |
+====================+========+===========+===================+
| ignoreBlankLines   | bool   | false     | 1.4.2             |
+--------------------+--------+-----------+-------------------+

Some of the rules this sniff enforces are that there should not be
whitespace at the end of a line, and that functions should not contain
multiple blank lines in a row. If the ``ignoreBlankLines`` property is
set to ``true``, blank lines (lines that contain only whitespace) may
have spaces and tabs as their content, and multiple blank lines will be
allows inside functions.

.. code:: xml

    <rule ref="Squiz.WhiteSpace.SuperfluousWhitespace">
        <properties>
            <property name="ignoreBlankLines" value="true" />
        </properties>
    </rule>
