<?php
$someObject->someFunction("some", "parameter")
->someOtherFunc(23, 42)->
    someOtherFunc2($one, $two)

 ->someOtherFunc3(23, 42)
    ->andAThirdFunction();

    $someObject->someFunction("some", "parameter")
        ->someOtherFunc(23, 42);

$someObject->someFunction("some", "parameter")->someOtherFunc(23, 42);

$someObject->someFunction("some", "parameter")
        ->someOtherFunc(23, 42);

func(
    $bar->foo()
)
    ->bar();

func(
    $bar->foo()
)
    ->bar(
        $bar->foo()
        ->bar()
            ->func()
    );

$object
    ->setBar($foo)
    ->setFoo($bar);

if ($bar) {
    $object
    ->setBar($foo)
    ->setFoo($bar);
}

$response -> CompletedTrackDetails -> TrackDetails -> Events;
$response
    -> CompletedTrackDetails
    -> TrackDetails
    -> Events;

$response
     -> CompletedTrackDetails
-> TrackDetails
 -> Events;

$var = get_object(
    $foo->something()
        ->query()
)->two()
    ->three();

$foo->one(
    $foo
            ->two()
);

get_object()->one()
    ->two()
        ->three();

someclass::one()
    ->two()
        ->three();

(new someclass())->one()
    ->two()
        ->three();

// phpcs:set PEAR.WhiteSpace.ObjectOperatorIndent multilevel true

$someObject
    ->startSomething()
                    ->someOtherFunc(23, 42)
->endSomething()
->doSomething(23, 42)
->endEverything();

$rootNode
    ->one()
        ->two()
            ->three()
        ->four()
    ->five();

$rootNode
    ->one()
        ->two()
            ->three()
    ->four()
    ->five();

$rootNode
    ->one()
    ->two()
        ->three()
    ->four()
->five();

$rootNode
    ->one()
        ->two()
            ->three()
        ->four()
        ->five();

// phpcs:set PEAR.WhiteSpace.ObjectOperatorIndent multilevel false

$object
    ?->setBar($foo)
    ?->setFoo($bar);

$someObject?->someFunction("some", "parameter")
->someOtherFunc(23, 42)?->
    someOtherFunc2($one, $two)

->someOtherFunc3(23, 42)
    ?->andAThirdFunction();

// phpcs:set PEAR.WhiteSpace.ObjectOperatorIndent multilevel true
$object
    ?->setBar($foo)
        ?->setFoo($bar);

$someObject?->someFunction("some", "parameter")
->someOtherFunc(23, 42)
        ?->someOtherFunc2($one, $two)

->someOtherFunc3(23, 42)
    ?->andAThirdFunction();
// phpcs:set PEAR.WhiteSpace.ObjectOperatorIndent multilevel false
