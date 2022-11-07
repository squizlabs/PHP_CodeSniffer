<?php

/* testNamespaceIsKeyword */
namespace SomeNamespace;

class ContextSensitiveKeywords
{
    const /* testAbstract */ ABSTRACT = 'ABSTRACT';
    const /* testArray */ ARRAY = 'ARRAY';
    const /* testAs */ AS = 'AS';
    const /* testBreak */ BREAK = 'BREAK';
    const /* testCallable */ CALLABLE = 'CALLABLE';
    const /* testCase */ CASE = 'CASE';
    const /* testCatch */ CATCH = 'CATCH';
    const /* testClass */ CLASS = 'CLASS';
    const /* testClone */ CLONE = 'CLONE';
    const /* testConst */ CONST = 'CONST';
    const /* testContinue */ CONTINUE = 'CONTINUE';
    const /* testDeclare */ DECLARE = 'DECLARE';
    const /* testDefault */ DEFAULT = 'DEFAULT';
    const /* testDo */ DO = 'DO';
    const /* testEcho */ ECHO = 'ECHO';
    const /* testElse */ ELSE = 'ELSE';
    const /* testElseIf */ ELSEIF = 'ELSEIF';
    const /* testEmpty */ EMPTY = 'EMPTY';
    const /* testEndDeclare */ ENDDECLARE = 'ENDDECLARE';
    const /* testEndFor */ ENDFOR = 'ENDFOR';
    const /* testEndForeach */ ENDFOREACH = 'ENDFOREACH';
    const /* testEndIf */ ENDIF = 'ENDIF';
    const /* testEndSwitch */ ENDSWITCH = 'ENDSWITCH';
    const /* testEndWhile */ ENDWHILE = 'ENDWHILE';
    const /* testEnum */ ENUM = 'ENUM';
    const /* testEval */ EVAL = 'EVAL';
    const /* testExit */ EXIT = 'EXIT';
    const /* testExtends */ EXTENDS = 'EXTENDS';
    const /* testFinal */ FINAL = 'FINAL';
    const /* testFinally */ FINALLY = 'FINALLY';
    const /* testFn */ FN = 'FN';
    const /* testFor */ FOR = 'FOR';
    const /* testForeach */ FOREACH = 'FOREACH';
    const /* testFunction */ FUNCTION = 'FUNCTION';
    const /* testGlobal */ GLOBAL = 'GLOBAL';
    const /* testGoto */ GOTO = 'GOTO';
    const /* testIf */ IF = 'IF';
    const /* testImplements */ IMPLEMENTS = 'IMPLEMENTS';
    const /* testInclude */ INCLUDE = 'INCLUDE';
    const /* testIncludeOnce */ INCLUDE_ONCE = 'INCLUDE_ONCE';
    const /* testInstanceOf */ INSTANCEOF = 'INSTANCEOF';
    const /* testInsteadOf */ INSTEADOF = 'INSTEADOF';
    const /* testInterface */ INTERFACE = 'INTERFACE';
    const /* testIsset */ ISSET = 'ISSET';
    const /* testList */ LIST = 'LIST';
    const /* testMatch */ MATCH = 'MATCH';
    const /* testNamespace */ NAMESPACE = 'NAMESPACE';
    const /* testNew */ NEW = 'NEW';
    const /* testParent */ PARENT = 'PARENT';
    const /* testPrint */ PRINT = 'PRINT';
    const /* testPrivate */ PRIVATE = 'PRIVATE';
    const /* testProtected */ PROTECTED = 'PROTECTED';
    const /* testPublic */ PUBLIC = 'PUBLIC';
    const /* testReadonly */ READONLY = 'READONLY';
    const /* testRequire */ REQUIRE = 'REQUIRE';
    const /* testRequireOnce */ REQUIRE_ONCE = 'REQUIRE_ONCE';
    const /* testReturn */ RETURN = 'RETURN';
    const /* testSelf */ SELF = 'SELF';
    const /* testStatic */ STATIC = 'STATIC';
    const /* testSwitch */ SWITCH = 'SWITCH';
    const /* testThrows */ THROW = 'THROW';
    const /* testTrait */ TRAIT = 'TRAIT';
    const /* testTry */ TRY = 'TRY';
    const /* testUnset */ UNSET = 'UNSET';
    const /* testUse */ USE = 'USE';
    const /* testVar */ VAR = 'VAR';
    const /* testWhile */ WHILE = 'WHILE';
    const /* testYield */ YIELD = 'YIELD';
    const /* testYieldFrom */ YIELD_FROM = 'YIELD_FROM';

    const /* testAnd */ AND = 'LOGICAL_AND';
    const /* testOr */ OR = 'LOGICAL_OR';
    const /* testXor */ XOR = 'LOGICAL_XOR';
}

namespace /* testKeywordAfterNamespaceShouldBeString */ class;
namespace /* testNamespaceNameIsString1 */ my\ /* testNamespaceNameIsString2 */ class\ /* testNamespaceNameIsString3 */ foreach;

/* testAbstractIsKeyword */ abstract /* testClassIsKeyword */ class SomeClass
    /* testExtendsIsKeyword */ extends SomeParent
    /* testImplementsIsKeyword */ implements SomeInterface
{
    /* testUseIsKeyword */ use SomeTrait, OtherTrait {
        SomeTrait::a /* testInsteadOfIsKeyword */ insteadof A;
        OtherTrait::a /* testAsIsKeyword */ as B;
    }

    /* testConstIsKeyword */ const CONSTANT = 'c';

    /* testPrivateIsKeyword */ private $private;
    /* testProtectedIsKeyword */ protected $protected;
    /* testPublicIsKeyword */ public $public;

    /* testVarIsKeyword */ var $var;
    /* testStaticIsKeyword */ static $static;

    /* testReadonlyIsKeyword */ readonly $readonly;

    /* testFinalIsKeyword */ final /* testFunctionIsKeyword */ function someFunction(
        /* testCallableIsKeyword */
        callable $callable,
        /* testSelfIsKeyword */
        self $self,
        /* testParentIsKeyword */
        parent $parent
    ) {
        /* testReturnIsKeyword */
        return $this;
    }
}

/* testInterfaceIsKeyword */ interface SomeInterface {}
/* testTraitIsKeyword */ trait SomeTrait {}
/* testEnumIsKeyword */ enum SomeEnum {}

$object = /* testNewIsKeyword */ new SomeClass();
$object /* testInstanceOfIsKeyword */ instanceof SomeClass;
$copy = /* testCloneIsKeyword */ clone $object;

/* testIfIsKeyword */ if (/* testEmptyIsKeyword */ empty($a)):
/* testElseIfIsKeyword */ elseif (false):
/* testElseIsKeyword */ else:
/* testEndIfIsKeyword */ endif;

/* testForIsKeyword */ for ($i = 0; $i < 100; $i++):
/* testEndForIsKeyword */ endfor;

/* testForeachIsKeyword */ foreach ($array as $value):
/* testEndForeachIsKeyword */ endforeach;

/* testSwitchIsKeyword */ switch (true):
    /* testCaseIsKeyword */case false:
        /* testBreakIsKeyword */
        break;
    /* testDefaultIsKeyword */ default:
        /* testContinueIsKeyword */
        continue;
/* testEndSwitchIsKeyword */ endswitch;

/* testDoIsKeyword */ do {

} while (false);

/* testWhileIsKeyword */ while (false):
/* testEndWhileIsKeyword */ endwhile;

/* testTryIsKeyword */
try {
    /* testThrowIsKeyword */
    throw new Exception();
} /* testCatchIsKeyword */ catch (Exception $e) {

} /* testFinallyIsKeyword */ finally {

}

/* testGlobalIsKeyword */
global $foo;
/* testEchoIsKeyword */
echo $foo;
/* testPrintIsKeyword */
print $foo;
/* testDieIsKeyword */
die($foo);
/* testEvalIsKeyword */
eval('<?php echo 5;');
/* testExitIsKeyword */
exit;
/* testIssetIsKeyword */
$a = isset($a);
/* testUnsetIsKeyword */
unset($a);

/* testIncludeIsKeyword */
include 'file.php';
/* testIncludeOnceIsKeyword */
include_once 'file.php';
/* testRequireIsKeyword */
require 'file.php';
/* testRequireOnceIsKeyword */
require_once 'file.php';

[$a, $b] = /* testListIsKeyword */ list($array);

/* testGotoIsKeyword */
goto label;

$match = /* testMatchIsKeyword */ match ($test) {
    1 => 'a',
    2 => 'b',
    /* testMatchDefaultIsKeyword */ default => 'default',
};

$closure = /* testFnIsKeyword */ fn () => 'string';

function () {
    /* testYieldIsKeyword */ yield $f;
    /* testYieldFromIsKeyword */ yield from someFunction();
};

/* testDeclareIsKeyword */ declare(ticks=1):
/* testEndDeclareIsKeyword */ enddeclare;

if (true /* testAndIsKeyword */ and false /* testOrIsKeyword */ or null /* testXorIsKeyword */ xor 0) {

}

$anonymousClass = new /* testAnonymousClassIsKeyword */ class {};
$anonymousClass2 = new class /* testExtendsInAnonymousClassIsKeyword */ extends SomeParent {};
$anonymousClass3 = new class /* testImplementsInAnonymousClassIsKeyword */ implements SomeInterface {};

$instantiated1 = new /* testClassInstantiationParentIsKeyword */ parent();
$instantiated2 = new /* testClassInstantiationSelfIsKeyword */ SELF;
$instantiated3 = new /* testClassInstantiationStaticIsKeyword */ static($param);

class Foo extends /* testNamespaceInNameIsKeyword */ namespace\Exception
{}

function /* testKeywordAfterFunctionShouldBeString */ eval() {}
function /* testKeywordAfterFunctionByRefShouldBeString */ &switch() {}
