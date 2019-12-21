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

    /**
     * @var Test|TestA|TestB|TestC|TestD|TestE
     */
    public $test;
}
