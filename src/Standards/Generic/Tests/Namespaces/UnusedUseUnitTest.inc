<?php

namespace My\Project;

use BarClass as Bar;
use Somewhere\Something;
use Foo;
use Route;
use ORM;
use Truc;
use Machin;
use Unused;
use Client, Customer as Clients, Somewhere\SomethingElse;
use My\Project\Test;
use My\Project\{TestA, TestB};
use My\{Project\TestC, TestD, Project\TestE};
use function SomeNamespace\fn_a;
use function SomeNamespace\fn_b;
use const SomeNamespace\ConstA;
use const SomeNamespace\ConstB;
use SomeNamespace\{ClassD, ClassF as F, ClassE};
use function SomeNamespace\{fn_d, fn_e};
use const SomeNamespace\{ConstD, ConstE};
use SomeException, SomeFooException;

class Container
{
    function test1(Bar $bar): ?Something
    {
        return null;
    }

    /**
     * @Foo
     * @Route("/{id}")
     * @ORM\Column(type="integer")
     */

    /**
     * @var Truc $truc
     */
    public $truc;

    /**
     * @param Toto $toto
     *
     * @return Machin|\Machine
     */
    function test2($toto)
    {
        $a = fn_a(ConstA);

        return $a;
    }

    public $SomethingElse;

    function test3()
    {
        /** @var Clients|Client[] $client */
        $client = [$this->SomethingElse];

        return $client;
    }

    function test4(ClassD $classD): F
    {
        $d = fn_d(ConstD);

        return $d;
    }

    function test5()
    {
        try {
            throw new SomeException();
        } catch (SomeFooException $e) {
            return;
        }
    }
}

class SecondContainer
{
    /**
     * @var Test|TestA|TestB|TestC|TestD|TestE
     */
    public $test;
}

namespace AnotherProject;

use Unused;
use Somewhere\SomethingElse;
use AnotherProject\Thing;

echo namespace\my_function();

class ThirdContainer
{
    /**
     * @var Thing|SomethingElse
     */
    public $test;
}

function () use ($tax) {}

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="whatever", uniqueConstraints={@UniqueConstraint(name="idx_whatever_code_UNIQUE", columns={"code"})})
 * @ORM\Entity
 */
class Whatever {}

namespace MyApp\ /* comment */ MyNamespace;

use MyApp\ /* comment */ MyClass1 ;
use \Hello\MyClass2;
use MyApp\MyNamespace\MyClass3;
use MyApp\ /* comment */ MyClass4;
use \Hello\MyClass5;
use MyApp /* comment */ \MyNamespace\MyClass6;
use \MyApp/*comment*/\MyNamespace\MyClass7;
use /* comment */DateTime;

new MyClass1();
new MyClass2();
new MyClass3();

namespace MyApp\MyNamespace;

use MyApp\Traits;
use MyApp\Traits\Used1;
use MyApp\Unused1;
use MyApp\Service\Used2;
use MyApp\Plugin\UsedInDocComment1;
use OtherNamespace\UsedInDocComment2 as AliasUsedInComment2;
use UsedInDocComment2;
use Unused2;
use Used3 as AliasUsed3;
use MyApp\MyNamespace\Used4;
use MyApp\MyNamespace\Used5 as AliasUsed5;
use MyApp\Used6;
use Used7 as AliasUsed7;
use Bar\Baz\Used8;
use Bar\Baz\Used9 as AliasUsed9;
use Bar\Baz\Unused3;
use Bar\Baz\Unused4 as AliasUnused4;
use FooBar\Unused5;
use Used10;
use Used11;
use FooBar\Used12 as AliasUsed12;
use FooBar\Used13;
use Unused6, Unused7;
use Used14;
use Unused5\Used15;
use Used16 as AliasUsed16;
use FooBar\Used17;
use FooBar\Used18;
use FooBar\Unused8;
use FooBar\Unused9;
use FooBaz\Unused10;
use BarBaz\Unused11;
use BarBaz\Unused12;
use BarBaz\Unused13;
use BarBaz\Unused14;
use Unused15;
use Used19;
use Used20;
use Used21;
use Used22;
use FooBar;
use FooB;
use Unused16;
use Unused17;
use Used23;
use Used24;
use Used25;
use Used26;
use Used27;
use Used28;

/**
 * @method Used23 myMethod(Used24 $param1, Used25 $param2)
 * @property Used26 $foo
 * @property-read Used27 $bar
 * @property-write Used28 $baz
 *
 * @Used10
 * @Used11\Table
 * @AliasUsed12(...)
 */
class Foo
{
    use Traits\MyTrait;
    use Traits\Used1;
    use Used17;

    private $unused2;

    protected function test1(Used1 $x, Used2 $y)
    {
    }

    /**
     * @return UsedInDocComment1
     */
    protected function test2()
    {
    }

    /**
     * @param UsedInDocComment2 $x
     */
    protected function test3($x)
    {
        /** @var AliasUsedInComment2 $y */
        $y = $x;
    }

    protected function test4(Used3 $x, Used4 $y)
    {
    }

    protected function test5(AliasUsed3 $x, AliasUsed5 $y)
    {
        new Used6();
        new AliasUsed7();
        Used8::create();
        AliasUsed9::create();

        // @var Unused5 $x
        $x = $y;

        /** @var Used13\MyType $x */
        $x = $y;
    }

    protected function test6() : Used14
    {
    }

    protected function test7() : Used15\MyClass
    {
    }

    protected function test8() : AliasUsed16
    {
        $this->unused2 = new \Unused1();
    }

    protected function test9() : Hey\Unused8\Foo
    {
        /** @var Bar\Unused9 $x */
        $x = new Bar\Unused10();
    }

    /**
     * @param
     * @Used19
     * @todo
     * @Used20
     * @todo Unused15
     */
    protected function test10() : Hey\ /* hey */ Unused11 /** comment */ \ Unused12
    {
        new Hey \ /** @todo */ Unused13();
        new /** hey */ Used18 \ World();
        MyClass::Unused14;
    }

    /**
     * @Used21(param=@Used22, value=@Used23, key=@FooBar, foo="@Unused16", bar="something @Unused17 bar")
     */
    protected function testAnnotations()
    {
    }
}
