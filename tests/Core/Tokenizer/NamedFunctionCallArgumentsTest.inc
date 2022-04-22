<?php

/* testPositionalArgs */
array_fill(START_INDEX, \COUNT, MyNS\VALUE);

/* testNamedArgs */
array_fill(start_index: 0, count: 100, value: 50);

/* testNamedArgsMultiline */
array_fill(
    start_index : 0,
    count       : 100,
    value       : 50,
);

/* testNamedArgsWithWhitespaceAndComments */
array_fill ( start_index : 0, /*comment */ count /*comment*/ : 100, value: 50);

/* testMixedPositionalAndNamedArgs */
htmlspecialchars($string, double_encode: false);

/* testNestedFunctionCallOuter */
array_fill(
    start_index: $obj->getPos(skip: false),
    count: count(array_or_countable: $array),
    value: 50
);

array_fill(
    start_index: /* testNestedFunctionCallInner1 */ $obj->getPos(skip: false),
    count: /* testNestedFunctionCallInner2 */ count(array_or_countable: $array),
    value: 50
);

/* testNamespaceOperatorFunction */
namespace\function_name(label:$string, more: false);

/* testNamespaceRelativeFunction */
Partially\Qualified\function_name(label:$string, more: false);

/* testNamespacedFQNFunction */
\Fully\Qualified\function_name(label: $string, more:false);

/* testVariableFunction */
$fn(label: $string, more:false);

/* testVariableVariableFunction */
${$fn}(label: $string, more:false);

/* testMethodCall */
$obj->methodName(label: $foo, more: $bar);

/* testVariableMethodCall */
$obj->{$var}(label: $foo, more: $bar);

/* testClassInstantiation */
$obj = new MyClass(label: $string, more:false);

/* testClassInstantiationSelf */
$obj = new self(label: $string, more:true);

/* testClassInstantiationStatic */
$obj = new static(label: $string, more:false);

/* testAnonClass */
$anon = new class(label: $string, more: false) {
    public function __construct($label, $more) {}
};

function myfoo( $💩💩💩, $Пасха, $_valid) {}
/* testNonAsciiNames */
foo(💩💩💩: [], Пасха: 'text', _valid: 123);

/* testMixedPositionalAndNamedArgsWithTernary */
foo( $cond ? true : false, name: $value2 );

/* testNamedArgWithTernary */
foo( label: $cond ? true : false, more: $cond ? CONSTANT_A : CONSTANT_B );

/* testTernaryWithFunctionCallsInThenElse */
echo $cond ? foo( label: $something ) : foo( more: $something_else );

/* testTernaryWithConstantsInThenElse */
echo $cond ? CONSTANT_NAME : OTHER_CONSTANT;

switch ($s) {
    /* testSwitchCaseWithConstant */
    case MY_CONSTANT:
        // Do something.
        break;

    /* testSwitchCaseWithClassProperty */
    case $obj->property:
        // Do something.
        break;

    /* testSwitchDefault */
    default:
        // Do something.
        break;
}

/* testTernaryWithClosuresAndReturnTypes */
$closure = $cond ? function() : bool {return true;} : function() : int {return 123;};

/* testTernaryWithArrowFunctionsAndReturnTypes */
$fn = $cond ? fn() : bool => true : fn() : int => 123;


/* testCompileErrorNamedBeforePositional */
// Not the concern of PHPCS. Should still be handled.
test(param: $bar, $foo);

/* testDuplicateName1 */
// Error Exception, but not the concern of PHPCS. Should still be handled.
test(param: 1, /* testDuplicateName2 */ param: 2);

/* testIncorrectOrderWithVariadic */
// Error Exception, but not the concern of PHPCS. Should still be handled.
array_fill(start_index: 0, ...[100, 50]);

/* testCompileErrorIncorrectOrderWithVariadic */
// Not the concern of PHPCS. Should still be handled.
test(...$values, param: $value); // Compile-time error

/* testParseErrorNoValue */
// Not the concern of PHPCS. Should still be handled.
test(param1:, param2:);

/* testParseErrorDynamicName */
// Parse error. Ignore.
function_name($variableStoringParamName: $value);

/* testParseErrorExit */
// Exit is a language construct, not a function. Named params not supported, handle it anyway.
exit(status: $value);

/* testParseErrorEmpty */
// Empty is a language construct, not a function. Named params not supported, handle it anyway.
empty(variable: $value);

/* testParseErrorEval */
// Eval is a language construct, not a function. Named params not supported, handle it anyway.
eval(code: $value);

/* testParseErrorArbitraryParentheses */
// Parse error. Not named param, handle it anyway.
$calc = (something: $value / $other);


/* testReservedKeywordAbstract1 */
foobar(abstract: $value, /* testReservedKeywordAbstract2 */ abstract: $value);

/* testReservedKeywordAnd1 */
foobar(and: $value, /* testReservedKeywordAnd2 */ and: $value);

/* testReservedKeywordArray1 */
foobar(array: $value, /* testReservedKeywordArray2 */ array: $value);

/* testReservedKeywordAs1 */
foobar(as: $value, /* testReservedKeywordAs2 */ as: $value);

/* testReservedKeywordBreak1 */
foobar(break: $value, /* testReservedKeywordBreak2 */ break: $value);

/* testReservedKeywordCallable1 */
foobar(callable: $value, /* testReservedKeywordCallable2 */ callable: $value);

/* testReservedKeywordCase1 */
foobar(case: $value, /* testReservedKeywordCase2 */ case: $value);

/* testReservedKeywordCatch1 */
foobar(catch: $value, /* testReservedKeywordCatch2 */ catch: $value);

/* testReservedKeywordClass1 */
foobar(class: $value, /* testReservedKeywordClass2 */ class: $value);

/* testReservedKeywordClone1 */
foobar(clone: $value, /* testReservedKeywordClone2 */ clone: $value);

/* testReservedKeywordConst1 */
foobar(const: $value, /* testReservedKeywordConst2 */ const: $value);

/* testReservedKeywordContinue1 */
foobar(continue: $value, /* testReservedKeywordContinue2 */ continue: $value);

/* testReservedKeywordDeclare1 */
foobar(declare: $value, /* testReservedKeywordDeclare2 */ declare: $value);

/* testReservedKeywordDefault1 */
foobar(default: $value, /* testReservedKeywordDefault2 */ default: $value);

/* testReservedKeywordDie1 */
foobar(die: $value, /* testReservedKeywordDie2 */ die: $value);

/* testReservedKeywordDo1 */
foobar(do: $value, /* testReservedKeywordDo2 */ do: $value);

/* testReservedKeywordEcho1 */
foobar(echo: $value, /* testReservedKeywordEcho2 */ echo: $value);

/* testReservedKeywordElse1 */
foobar(else: $value, /* testReservedKeywordElse2 */ else: $value);

/* testReservedKeywordElseif1 */
foobar(elseif: $value, /* testReservedKeywordElseif2 */ elseif: $value);

/* testReservedKeywordEmpty1 */
foobar(empty: $value, /* testReservedKeywordEmpty2 */ empty: $value);

/* testReservedKeywordEnddeclare1 */
foobar(enddeclare: $value, /* testReservedKeywordEnddeclare2 */ enddeclare: $value);

/* testReservedKeywordEndfor1 */
foobar(endfor: $value, /* testReservedKeywordEndfor2 */ endfor: $value);

/* testReservedKeywordEndforeach1 */
foobar(endforeach: $value, /* testReservedKeywordEndforeach2 */ endforeach: $value);

/* testReservedKeywordEndif1 */
foobar(endif: $value, /* testReservedKeywordEndif2 */ endif: $value);

/* testReservedKeywordEndswitch1 */
foobar(endswitch: $value, /* testReservedKeywordEndswitch2 */ endswitch: $value);

/* testReservedKeywordEndwhile1 */
foobar(endwhile: $value, /* testReservedKeywordEndwhile2 */ endwhile: $value);

/* testReservedKeywordEval1 */
foobar(eval: $value, /* testReservedKeywordEval2 */ eval: $value);

/* testReservedKeywordExit1 */
foobar(exit: $value, /* testReservedKeywordExit2 */ exit: $value);

/* testReservedKeywordExtends1 */
foobar(extends: $value, /* testReservedKeywordExtends2 */ extends: $value);

/* testReservedKeywordFinal1 */
foobar(final: $value, /* testReservedKeywordFinal2 */ final: $value);

/* testReservedKeywordFinally1 */
foobar(finally: $value, /* testReservedKeywordFinally2 */ finally: $value);

/* testReservedKeywordFn1 */
foobar(fn: $value, /* testReservedKeywordFn2 */ fn: $value);

/* testReservedKeywordFor1 */
foobar(for: $value, /* testReservedKeywordFor2 */ for: $value);

/* testReservedKeywordForeach1 */
foobar(foreach: $value, /* testReservedKeywordForeach2 */ foreach: $value);

/* testReservedKeywordFunction1 */
foobar(function: $value, /* testReservedKeywordFunction2 */ function: $value);

/* testReservedKeywordGlobal1 */
foobar(global: $value, /* testReservedKeywordGlobal2 */ global: $value);

/* testReservedKeywordGoto1 */
foobar(goto: $value, /* testReservedKeywordGoto2 */ goto: $value);

/* testReservedKeywordIf1 */
foobar(if: $value, /* testReservedKeywordIf2 */ if: $value);

/* testReservedKeywordImplements1 */
foobar(implements: $value, /* testReservedKeywordImplements2 */ implements: $value);

/* testReservedKeywordInclude1 */
foobar(include: $value, /* testReservedKeywordInclude2 */ include: $value);

/* testReservedKeywordInclude_once1 */
foobar(include_once: $value, /* testReservedKeywordInclude_once2 */ include_once: $value);

/* testReservedKeywordInstanceof1 */
foobar(instanceof: $value, /* testReservedKeywordInstanceof2 */ instanceof: $value);

/* testReservedKeywordInsteadof1 */
foobar(insteadof: $value, /* testReservedKeywordInsteadof2 */ insteadof: $value);

/* testReservedKeywordInterface1 */
foobar(interface: $value, /* testReservedKeywordInterface2 */ interface: $value);

/* testReservedKeywordIsset1 */
foobar(isset: $value, /* testReservedKeywordIsset2 */ isset: $value);

/* testReservedKeywordList1 */
foobar(list: $value, /* testReservedKeywordList2 */ list: $value);

/* testReservedKeywordMatch1 */
foobar(match: $value, /* testReservedKeywordMatch2 */ match: $value);

/* testReservedKeywordNamespace1 */
foobar(namespace: $value, /* testReservedKeywordNamespace2 */ namespace: $value);

/* testReservedKeywordNew1 */
foobar(new: $value, /* testReservedKeywordNew2 */ new: $value);

/* testReservedKeywordOr1 */
foobar(or: $value, /* testReservedKeywordOr2 */ or: $value);

/* testReservedKeywordPrint1 */
foobar(print: $value, /* testReservedKeywordPrint2 */ print: $value);

/* testReservedKeywordPrivate1 */
foobar(private: $value, /* testReservedKeywordPrivate2 */ private: $value);

/* testReservedKeywordProtected1 */
foobar(protected: $value, /* testReservedKeywordProtected2 */ protected: $value);

/* testReservedKeywordPublic1 */
foobar(public: $value, /* testReservedKeywordPublic2 */ public: $value);

/* testReservedKeywordReadonly1 */
foobar(readonly: $value, /* testReservedKeywordReadonly2 */ readonly: $value);

/* testReservedKeywordRequire1 */
foobar(require: $value, /* testReservedKeywordRequire2 */ require: $value);

/* testReservedKeywordRequire_once1 */
foobar(require_once: $value, /* testReservedKeywordRequire_once2 */ require_once: $value);

/* testReservedKeywordReturn1 */
foobar(return: $value, /* testReservedKeywordReturn2 */ return: $value);

/* testReservedKeywordStatic1 */
foobar(static: $value, /* testReservedKeywordStatic2 */ static: $value);

/* testReservedKeywordSwitch1 */
foobar(switch: $value, /* testReservedKeywordSwitch2 */ switch: $value);

/* testReservedKeywordThrow1 */
foobar(throw: $value, /* testReservedKeywordThrow2 */ throw: $value);

/* testReservedKeywordTrait1 */
foobar(trait: $value, /* testReservedKeywordTrait2 */ trait: $value);

/* testReservedKeywordTry1 */
foobar(try: $value, /* testReservedKeywordTry2 */ try: $value);

/* testReservedKeywordUnset1 */
foobar(unset: $value, /* testReservedKeywordUnset2 */ unset: $value);

/* testReservedKeywordUse1 */
foobar(use: $value, /* testReservedKeywordUse2 */ use: $value);

/* testReservedKeywordVar1 */
foobar(var: $value, /* testReservedKeywordVar2 */ var: $value);

/* testReservedKeywordWhile1 */
foobar(while: $value, /* testReservedKeywordWhile2 */ while: $value);

/* testReservedKeywordXor1 */
foobar(xor: $value, /* testReservedKeywordXor2 */ xor: $value);

/* testReservedKeywordYield1 */
foobar(yield: $value, /* testReservedKeywordYield2 */ yield: $value);

/* testReservedKeywordInt1 */
foobar(int: $value, /* testReservedKeywordInt2 */ int: $value);

/* testReservedKeywordFloat1 */
foobar(float: $value, /* testReservedKeywordFloat2 */ float: $value);

/* testReservedKeywordBool1 */
foobar(bool: $value, /* testReservedKeywordBool2 */ bool: $value);

/* testReservedKeywordString1 */
foobar(string: $value, /* testReservedKeywordString2 */ string: $value);

/* testReservedKeywordTrue1 */
foobar(true: $value, /* testReservedKeywordTrue2 */ true: $value);

/* testReservedKeywordFalse1 */
foobar(false: $value, /* testReservedKeywordFalse2 */ false: $value);

/* testReservedKeywordNull1 */
foobar(null: $value, /* testReservedKeywordNull2 */ null: $value);

/* testReservedKeywordVoid1 */
foobar(void: $value, /* testReservedKeywordVoid2 */ void: $value);

/* testReservedKeywordIterable1 */
foobar(iterable: $value, /* testReservedKeywordIterable2 */ iterable: $value);

/* testReservedKeywordObject1 */
foobar(object: $value, /* testReservedKeywordObject2 */ object: $value);

/* testReservedKeywordResource1 */
foobar(resource: $value, /* testReservedKeywordResource2 */ resource: $value);

/* testReservedKeywordMixed1 */
foobar(mixed: $value, /* testReservedKeywordMixed2 */ mixed: $value);

/* testReservedKeywordNumeric1 */
foobar(numeric: $value, /* testReservedKeywordNumeric2 */ numeric: $value);

/* testReservedKeywordParent1 */
foobar(parent: $value, /* testReservedKeywordParent2 */ parent: $value);

/* testReservedKeywordSelf1 */
foobar(self: $value, /* testReservedKeywordSelf2 */ self: $value);

/* testReservedKeywordNever1 */
foobar(never: $value, /* testReservedKeywordNever2 */ never: $value);
