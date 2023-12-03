<?php

enum Foo
{
    /* testPureEnumCase */
    case SOME_CASE;
}

enum Boo: int {
    /* testBackingIntegerEnumCase */
    case ONE = 1;
}

enum Hoo: string
{
    /* testBackingStringEnumCase */
    case ONE = 'one';
}

enum ComplexEnum: int implements SomeInterface
{
    use SomeTrait {
        traitMethod as enumMethod;
    }

    const SOME_CONSTANT = true;

    /* testEnumCaseInComplexEnum */
    case ONE = 1;

    /* testEnumCaseIsCaseInsensitive */
    CaSe TWO = 2;

    public function someMethod(): bool
    {
        switch (true) {
            /* testCaseWithSemicolonIsNotEnumCase */
            case CONSTANT;
        }
    }

    /* testEnumCaseAfterSwitch */
    case THREE = 3;

    public function someOtherMethod(): bool
    {
        switch (true):
            case false:
        endswitch;
    }

    /* testEnumCaseAfterSwitchWithEndSwitch */
    case FOUR = 4;
}

switch (true) {
    /* testCaseWithConstantIsNotEnumCase */
    case CONSTANT:
    /* testCaseWithConstantAndIdenticalIsNotEnumCase */
    case CONSTANT === 1:
    /* testCaseWithAssigmentToConstantIsNotEnumCase */
    case CONSTANT = 1:
    /* testIsNotEnumCaseIsCaseInsensitive */
    cAsE CONSTANT:
}

switch ($x) {
    /* testCaseInSwitchWhenCreatingEnumInSwitch1 */
    case 'a': {
        enum Foo {}
        break;
    }

    /* testCaseInSwitchWhenCreatingEnumInSwitch2 */
    case 'b';
        enum Bar {}
        break;
}

enum Foo: string {
    /* testKeywordAsEnumCaseNameShouldBeString1 */
    case INTERFACE = 'interface';
    /* testKeywordAsEnumCaseNameShouldBeString2 */
    case TRAIT = 'trait';
    /* testKeywordAsEnumCaseNameShouldBeString3 */
    case ENUM = 'enum';
    /* testKeywordAsEnumCaseNameShouldBeString4 */
    case FUNCTION = 'function';
    /* testKeywordAsEnumCaseNameShouldBeString5 */
    case FALSE = 'false';
    /* testKeywordAsEnumCaseNameShouldBeString6 */
    case DEFAULT = 'default';
    /* testKeywordAsEnumCaseNameShouldBeString7 */
    case ARRAY = 'array';
}
