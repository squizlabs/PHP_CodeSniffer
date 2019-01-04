PHP_CodeSniffer allows developers to design their own coding standards by creating a simple ruleset XML file that both pulls in sniffs from existing standards and customises them for the developer's needs. This XML file can be named anything you like, as long as it has an `xml` extension and complies to the ruleset.xml format. The file can be stored anywhere, making it perfect for placing under version control with a project's source code and unit tests.

Once created, a ruleset file can be used with the `--standard` command line argument. In the following example, PHP_CodeSniffer will use the coding standard defined in a custom ruleset file called custom_ruleset.xml:

    $ phpcs --standard=/path/to/custom_ruleset.xml test.php

## The Annotated Sample File

The following sample file documents the ruleset.xml format and shows you the complete range of features that the format supports. The file is designed for documentation purposes only and is not a working coding standard.

```xml
<?xml version="1.0"?>
<ruleset name="Custom Standard" namespace="MyProject\CS\Standard">

 <!--
    The name attribute of the ruleset tag is displayed
    when running PHP_CodeSniffer with the -v command line
    argument.

    If you have custom sniffs, and they use a namespace prefix
    that is different to the name of the directory containing
    your ruleset.xml file, you can set the namespace prefix using
    the namespace attribute of the ruleset tag.

    For example, if your namespace format for sniffs is
    MyProject\CS\Standard\Sniffs\Category, set the namespace to
    MyProject\CS\Standard (everything up to \Sniffs\)
 -->

 <!--
    The content of the description tag is not displayed anywhere
    except in this file, so it can contain information for
    developers who may change this file in the future.
 -->
 <description>A custom coding standard</description>

 <!--
    You can hard-code config values used by sniffs directly
    into your custom standard. Note that this does not work
    for config values that override command line arguments,
    such as show_warnings and report_format.
    
    The following tag is equivalent to the command line argument:
    --runtime-set zend_ca_path /path/to/ZendCodeAnalyzer
 -->
 <config name="zend_ca_path" value="/path/to/ZendCodeAnalyzer"/>

<!--
    If no files or directories are specified on the command line
    your custom standard can specify what files should be checked
    instead.

    Note that file and directory paths specified in a ruleset are
    relative to the ruleset's location, and that specifying any file or
    directory path on the command line will ignore all file tags.
 -->
 <file>./path/to/directory</file>
 <file>./path/to/file.php</file>

 <!--
    You can hard-code ignore patterns directly into your
    custom standard so you don't have to specify the
    patterns on the command line.
    
    The following two tags are equivalent to the command line argument:
    --ignore=*/tests/*,*/data/*
 -->
 <exclude-pattern>*/tests/*</exclude-pattern>
 <exclude-pattern>*/data/*</exclude-pattern>

<!--
    Patterns can be specified as relative if you would
    like the relative path of the file checked instead of the
    full path. This can sometimes help with portability.
    
    The relative path is determined based on the paths you
    pass into PHP_CodeSniffer on the command line.
 -->
 <exclude-pattern type="relative">^/tests/*</exclude-pattern>
 <exclude-pattern type="relative">^/data/*</exclude-pattern>

 <!--
    You can hard-code command line values into your custom standard.
    Note that this does not work for the command line values:
    -v[v][v], -l, -d, --sniffs and --standard
    
    The following tags are equivalent to the command line arguments:
    --report=summary --colors -sp
 -->
 <arg name="report" value="summary"/>
 <arg name="colors"/>
 <arg value="sp"/>

 <!--
    You can hard-code custom php.ini settings into your custom standard.
    The following tag sets the memory limit to 64M.
 -->
 <ini name="memory_limit" value="64M"/>

 <!--
    If your helper classes need custom autoloading rules that you are
    not able to include in other ways, you can hard-code files to include
    before the ruleset is processed and any sniff classes have been loaded.

    This is different to bootstrap files, which are loaded after the ruleset
    has already been processed.
 -->
 <autoload>/path/to/autoload.php</autoload>
 <autoload>/path/to/other/autoload.php</autoload>

 <!--
    Include all sniffs in the PEAR standard. Note that the
    path to the standard does not have to be specified as the
    PEAR standard exists inside the PHP_CodeSniffer install
    directory.
 -->
 <rule ref="PEAR"/>

 <!--
    Include all sniffs in an external standard directory. Note
    that we have to specify the full path to the standard's
    directory because it does not exist inside the PHP_CodeSniffer
    install directory.
 -->
 <rule ref="/home/username/standards/mystandard"/>

 <!--
    Include everything in another ruleset.xml file. This is
    really handy if you want to customise another developer's
    custom standard. They just need to distribute their single
    ruleset file to allow this.
 -->
 <rule ref="/home/username/standards/custom.xml"/>

 <!--
    Relative paths can also be used everywhere absolute paths are used.
    Make sure the reference starts with ./ or ../ so PHP_CodeSniffer
    knows it is a relative path.
 -->
 <rule ref="./standards/mystandard"/>
 <rule ref="../username/custom.xml"/>

 <!--
    Include all sniffs in the Squiz standard except one. Note that
    the name of the sniff being excluded is the code that the sniff
    is given by PHP_CodeSniffer and is based on the file name and
    path of the sniff class. You can display these codes using the
    -s command line argument when checking a file.
 -->
 <rule ref="Squiz">
  <exclude name="Squiz.PHP.CommentedOutCode"/>
 </rule>

 <!--
    You can also exclude a single sniff message.
 -->
 <rule ref="Squiz">
  <exclude name="Squiz.Strings.DoubleQuoteUsage.ContainsVar"/>
 </rule>

 <!--
    Or, you can exclude a whole category of sniffs.
 -->
 <rule ref="Squiz">
  <exclude name="Squiz.PHP"/>
 </rule>

<!--
    You can even exclude a whole standard. This example includes
    all sniffs from the Squiz standard, but excludes any that come
    from the Generic standard.
 -->
 <rule ref="Squiz">
  <exclude name="Generic"/>
 </rule>

 <!--
    Include some specific sniffs from the Generic standard.
    Note again that the name of the sniff is the code that
    PHP_CodeSniffer gives it.
 -->
 <rule ref="Generic.CodeAnalysis.UnusedFunctionParameter"/>
 <rule ref="Generic.Commenting.Todo"/>
 <rule ref="Generic.ControlStructures.InlineControlStructure"/>

 <!--
    If you are including sniffs that are not installed, you can
    reference the sniff class using an absolute or relative path
    instead of using the sniff code.
 -->
 <rule ref="/path/to/standards/Generic/Sniffs/Commenting/TodoSniff.php"/>
 <rule ref="../Generic/Sniffs/ControlStructures/InlineControlStructureSniff.php"/>

 <!--
    Here we are including a specific sniff but also changing
    the error message of a specific message inside the sniff.
    Note that the specific code for the message, which is
    CommentFound in this case, is defined by the sniff developer.
    You can display these codes by using the -s command line
    argument when checking a file.

    Also note that this message has a variable inside it,
    which is why it is important that sniffs use a printf style
    format for their error messages.

    We also drop the severity of this message from the
    default value (5) so that it is hidden by default. It can be
    displayed by setting the minimum severity on the PHP_CodeSniffer
    command line. This is great if you want to use some messages
    only in code reviews and not have them block code commits.
 -->
 <rule ref="Generic.Commenting.Todo.CommentFound">
  <message>Please review this TODO comment: %s</message>
  <severity>3</severity>
 </rule>

 <!--
    You can also change the type of a message from error to
    warning and vice versa.
 -->
 <rule ref="Generic.Commenting.Todo.CommentFound">
  <type>error</type>
 </rule>
 <rule ref="Squiz.Strings.DoubleQuoteUsage.ContainsVar">
  <type>warning</type>
 </rule>

 <!--
    Here we change two messages from the same sniff. Note how the
    codes are slightly different because the sniff developer has
    defined both a MaxExceeded message and a TooLong message. In the
    case of this sniff, one is used for warnings and one is used
    for errors.
 -->
 <rule ref="Generic.Files.LineLength.MaxExceeded">
  <message>Line contains %2$s chars, which is more than the limit of %1$s</message>
 </rule>
 <rule ref="Generic.Files.LineLength.TooLong">
  <message>Line longer than %s characters; contains %s characters</message>
 </rule>

 <!--
    Some sniffs have public member vars that allow you to
    customise specific elements of the sniff. In the case of
    the Generic LineLength sniff, you can customise the limit
    at which the sniff will throw warnings and the limit at
    which it will throw errors.

    The rule below includes the LineLength sniff but changes the
    settings so the sniff will show warnings for any line longer
    than 90 chars and errors for any line longer than 100 chars.
 -->
 <rule ref="Generic.Files.LineLength">
  <properties>
   <property name="lineLimit" value="90"/>
   <property name="absoluteLineLimit" value="100"/>
  </properties>
 </rule>

 <!--
    Another useful example of changing sniff settings is
    to specify the end of line character that your standard
    should check for.
 -->
 <rule ref="Generic.Files.LineEndings">
  <properties>
   <property name="eolChar" value="\r\n"/>
  </properties>
 </rule>

 <!--
    Boolean values should be specified by using the strings
    "true" and "false" rather than the integers 0 and 1.
 -->
 <rule ref="Generic.Formatting.MultipleStatementAlignment">
  <properties>
   <property name="maxPadding" value="8"/>
   <property name="ignoreMultiLine" value="true"/>
   <property name="error" value="true"/>
  </properties>
 </rule>
 
<!--
    Array values are specified by using "element" tags
    with "key" and "value" attributes.

    NOTE: This syntax is is only supported in PHP_CodeSniffer
    versions 3.3.0 and greater.
 -->
 <rule ref="Generic.PHP.ForbiddenFunctions">
  <properties>
   <property name="forbiddenFunctions" type="array">
    <element key="delete" value="unset"/>
    <element key="print" value="echo"/>
    <element key="create_function" value="null"/>
   </property>
  </properties>
 </rule>

 <!--
    Before version 3.3.0, array values are specified by using a string
    representation of the array.

    NOTE: This syntax is deprecated and will be removed in
    PHP_CodeSniffer version 4.0
 -->
 <rule ref="Generic.PHP.ForbiddenFunctions">
  <properties>
   <property name="forbiddenFunctions" type="array" value="delete=>unset,print=>echo,create_function=>null" />
  </properties>
 </rule>

 <!--
    If you are including another standard, some array properties may
    have already been defined. Instead of having to redefine them
    you can choose to extend the property value instead. Any elements with
    new keys will be added to the property value, and any elements with
    existing keys will override the imported value.

    NOTE: This syntax is is only supported in PHP_CodeSniffer
    versions 3.4.0 and greater.
 -->
 <rule ref="Generic.PHP.ForbiddenFunctions">
  <properties>
   <property name="forbiddenFunctions" type="array" extend="true">
    <element key="sizeof" value="count"/>
   </property>
  </properties>
 </rule>

 <!--
    If you want to completely disable an error message in a sniff
    but you don't want to exclude the whole sniff, you can
    change the severity of the message to 0. In this case, we
    want the Squiz DoubleQuoteUsage sniff to be included in our
    standard, but we don't want the ContainsVar error message to
    ever be displayed.
 -->
 <rule ref="Squiz.Strings.DoubleQuoteUsage.ContainsVar">
  <severity>0</severity>
 </rule>

 <!--
    There is a special internal error message produced by PHP_CodeSniffer
    when it is unable to detect code in a file, possible due to
    the use of short open tags even though php.ini disables them.
    You can disable this message in the same way as sniff messages.

    Again, the code here will be displayed in the PHP_CodeSniffer
    output when using the -s command line argument while checking a file.
 -->
 <rule ref="Internal.NoCodeFound">
  <severity>0</severity>
 </rule>

 <!--
    You can hard-code ignore patterns for specific sniffs,
    a feature not available on the command line. Please note that
    all sniff-specific ignore patterns are checked using absolute paths.

    The code here will hide all messages from the Squiz DoubleQuoteUsage
    sniff for files that match either of the two exclude patterns.
 -->
 <rule ref="Squiz.Strings.DoubleQuoteUsage">
    <exclude-pattern>*/tests/*</exclude-pattern>
    <exclude-pattern>*/data/*</exclude-pattern>
 </rule>

 <!--
    You can also be more specific and just exclude some messages.
    Please note that all message-specific ignore patterns are
    checked using absolute paths.

    The code here will just hide the ContainsVar error generated by the
    Squiz DoubleQuoteUsage sniff for files that match either of the two
    exclude patterns.
 -->
 <rule ref="Squiz.Strings.DoubleQuoteUsage.ContainsVar">
    <exclude-pattern>*/tests/*</exclude-pattern>
    <exclude-pattern>*/data/*</exclude-pattern>
 </rule>

  <!--
    You can hard-code include patterns for specific sniffs,
    allowing you to only include sniffs when checking specific files.
    Please note that all sniff-specific include patterns are checked using
    absolute paths.

    The code here will only run the Squiz DoubleQuoteUsage sniff for
    files that match either of the two include patterns.
 -->
 <rule ref="Squiz.Strings.DoubleQuoteUsage">
    <include-pattern>*/templates/*</include-pattern>
    <include-pattern>*.tpl</include-pattern>
 </rule>

 <!--
    As with exclude rules, you can be more specific and just include
    some messages. Please note that all message-specific include patterns
    are checked using absolute paths.

    The code here will just show the ContainsVar error generated by the
    Squiz DoubleQuoteUsage sniff for files that match either of the two
    include patterns.
 -->
 <rule ref="Squiz.Strings.DoubleQuoteUsage.ContainsVar">
    <include-pattern>*/templates/*</include-pattern>
    <include-pattern>*.tpl</include-pattern>
 </rule>

</ruleset>
```

## Selectively Applying Rules

All tags in a ruleset file, with the exception of `ruleset` and `description`, can be selectively applied when a specific tool is being run. The two tools that are available are `phpcs` (the coding standards checker) and `phpcbf` (the coding standards fixer). Restrictions are applied by using the `phpcs-only` and `phpcbf-only` tag attributes.

Setting the `phpcs-only` attribute to `true` will only apply the rule when the `phpcs` tool is running. The rule will not be applied while the file is being fixed with the `phpcbf` tool.

Setting the `phpcbf-only` attribute to `true` will only apply the rule when the `phpcbf` tool is fixing a file. The rule will not be applied while the file is being checked with the `phpcs` tool.

The following sample file shows a ruleset.xml file that makes use of selective rules. The file is designed for documentation purposes only and is not a working coding standard.

```xml
<?xml version="1.0"?>
<ruleset name="Selective Standard">

 <!--
    Use an external tool only when checking coding standards
    and not while fixing a file.
 -->
 <config phpcs-only="true" name="zend_ca_path" value="/path/to/ZendCodeAnalyzer"/>

 <!--
    Exclude some files from being fixed.
 -->
 <exclude-pattern phpcbf-only="true">*/3rdparty/*</exclude-pattern>

 <!--
    Exclude some sniffs when fixing files, but allow them
    to still report errors while checking files.
 -->
 <rule ref="Squiz">
  <exclude phpcbf-only="true" name="Generic.WhiteSpace.ScopeIndent"/>
 </rule>

 <!--
    Exclude some messages when fixing files, but allow them
    to still report errors while checking files.
 -->
 <rule ref="Generic.Commenting.Todo.CommentFound">
  <severity phpcbf-only="true">0</severity>
 </rule>

 <!--
    Set different property values for fixing and checking.
 -->
 <rule ref="Generic.Files.LineLength">
  <properties>
   <property phpcs-only="true" name="lineLimit" value="80"/>
   <property phpcbf-only="true" name="lineLimit" value="120"/>
  </properties>
 </rule>

</ruleset>
```