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
