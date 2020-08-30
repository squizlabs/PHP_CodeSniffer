<?php

/* testNamespaceDeclaration */
namespace Package;

/* testNamespaceDeclarationWithLevels */
namespace Vendor\SubLevel\Domain;

/* testUseStatement */
use ClassName;

/* testUseStatementWithLevels */
use Vendor\Level\Domain;

/* testFunctionUseStatement */
use function function_name;

/* testFunctionUseStatementWithLevels */
use function Vendor\Level\function_in_ns;

/* testConstantUseStatement */
use const CONSTANT_NAME;

/* testConstantUseStatementWithLevels */
use const Vendor\Level\OTHER_CONSTANT;

/* testMultiUseUnqualified */
use UnqualifiedClassName,
    /* testMultiUsePartiallyQualified */
    Sublevel\PartiallyClassName;

/* testGroupUseStatement */
use Vendor\Level\{
    AnotherDomain,
    function function_grouped,
    const CONSTANT_GROUPED,
    Sub\YetAnotherDomain,
    function SubLevelA\function_grouped_too,
    const SubLevelB\CONSTANT_GROUPED_TOO,
};

/* testClassName */
class MyClass
    /* testExtendedFQN */
    extends \Vendor\Level\FQN
    /* testImplementsRelative */
    implements namespace\Name,
        /* testImplementsFQN */
        \Fully\Qualified,
        /* testImplementsUnqualified */
        Unqualified,
        /* testImplementsPartiallyQualified */
        Sub\Level\Name
{
    /* testFunctionName */
    public function function_name(
        /* testTypeDeclarationRelative */
        ?namespace\Name|object $paramA,

        /* testTypeDeclarationFQN */
        \Fully\Qualified\Name $paramB,

        /* testTypeDeclarationUnqualified */
        Unqualified|false $paramC,

        /* testTypeDeclarationPartiallyQualified */
        ?Sublevel\Name $paramD,

    /* testReturnTypeFQN */
    ) : ?\Name {

        try {
            /* testFunctionCallRelative */
            echo NameSpace\function_name();

            /* testFunctionCallFQN */
            echo \Vendor\Package\function_name();

            /* testFunctionCallUnqualified */
            echo function_name();

            /* testFunctionPartiallyQualified */
            echo Level\function_name();

        /* testCatchRelative */
        } catch (namespace\SubLevel\Exception $e) {

        /* testCatchFQN */
        } catch (\Exception $e) {

        /* testCatchUnqualified */
        } catch (Exception $e) {

        /* testCatchPartiallyQualified */
        } catch (Level\Exception $e) {
        }

        /* testNewRelative */
        $obj = new namespace\ClassName();

        /* testNewFQN */
        $obj = new \Vendor\ClassName();

        /* testNewUnqualified */
        $obj = new ClassName;

        /* testNewPartiallyQualified */
        $obj = new Level\ClassName;

        /* testDoubleColonRelative */
        $value = namespace\ClassName::property;

        /* testDoubleColonFQN */
        $value = \ClassName::static_function();

        /* testDoubleColonUnqualified */
        $value = ClassName::CONSTANT_NAME;

        /* testDoubleColonPartiallyQualified */
        $value = Level\ClassName::CONSTANT_NAME['key'];
        
        /* testInstanceOfRelative */
        $is = $obj instanceof namespace\ClassName;

        /* testInstanceOfFQN */
        if ($obj instanceof \Full\ClassName) {}

        /* testInstanceOfUnqualified */
        if ($a === $b && $obj instanceof ClassName && true) {}

        /* testInstanceOfPartiallyQualified */
        $is = $obj instanceof Partially\ClassName;
    }
}

/* testInvalidInPHP8Whitespace */
namespace \ Sublevel
          \ function_name();

/* testInvalidInPHP8Comments */
$value = \Fully
    // phpcs:ignore Stnd.Cat.Sniff -- for reasons
    \Qualified
    /* comment */
    \Name
    // comment
    :: function_name();
