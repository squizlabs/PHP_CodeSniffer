<?php

/* testPureEnum */
enum Foo
{
    case SOME_CASE;
}

/* testBackedIntEnum */
enum Boo: int {
    case ONE = 1;
    case TWO = 1;
}

/* testBackedStringEnum */
enum Hoo : string
{
    case ONE = 'one';
    case TWO = 'two';
}

/* testComplexEnum */
enum ComplexEnum: int implements SomeInterface
{
    use SomeTrait {
        traitMethod as enumMethod;
    }

    const SOME_CONSTANT = true;

    case ONE = 1;

    public function someMethod(): bool
    {
    }
}

/* testEnumWithEnumAsClassName */
enum /* testEnumAsClassNameAfterEnumKeyword */ Enum {}

/* testEnumIsCaseInsensitive */
EnUm Enum {}

/* testEnumUsedAsClassName */
class Enum {
    /* testEnumUsedAsClassConstantName */
    const ENUM = 'enum';

    /* testEnumUsedAsMethodName */
    public function enum() {
        // Do something.

        /* testEnumUsedAsPropertyName */
        $this->enum = 'foo';
    }
}

/* testEnumUsedAsFunctionName */
function enum()
{
}

/* testDeclarationContainingComment */
enum /* comment */ Name
{
    case SOME_CASE;
}

enum /* testEnumUsedAsEnumName */ Enum
{
}

/* testEnumUsedAsNamespaceName */
namespace Enum;
/* testEnumUsedAsPartOfNamespaceName */
namespace My\Enum\Collection;
/* testEnumUsedInObjectInitialization */
$obj = new Enum;
/* testEnumAsFunctionCall */
$var = enum($a, $b);
/* testEnumAsFunctionCallWithNamespace */
var = namespace\enum();
/* testClassConstantFetchWithEnumAsClassName */
echo Enum::CONSTANT;
/* testClassConstantFetchWithEnumAsConstantName */
echo ClassName::ENUM;

/* testParseErrorMissingName */
enum {
    case SOME_CASE;
}

/* testParseErrorLiveCoding */
// This must be the last test in the file.
enum
