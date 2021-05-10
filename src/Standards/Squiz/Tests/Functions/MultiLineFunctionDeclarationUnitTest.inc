<?php
public function someFunctionWithAVeryLongName($firstParameter='something',
   $secondParameter='booooo',
   $third=null, $fourthParameter=false,
     $fifthParameter=123.12,
    $sixthParam=true
){
}

function someFunctionWithAVeryLongName2(
$firstParameter='something',
$secondParameter='booooo',
)  {
}

function blah() {
}

function blah()
{
}

class MyClass
{

    public function someFunctionWithAVeryLongName(
        $firstParameter='something',
        $secondParameter='booooo',
        $third=null,
        $fourthParameter=false,
        $fifthParameter=123.12,
        $sixthParam=true
    ) /** w00t */ {
    }

    public function someFunctionWithAVeryLongName2($firstParameter='something',
        $secondParameter='booooo',
        $third=null
    ) {
    }

     public function someFunctionWithAVeryLongName3(
         $firstParameter, $secondParameter, $third=null
     ) {
     }

     public function someFunctionWithAVeryLongName4(
         $firstParameter, $secondParameter
     ) {
     }

     public function someFunctionWithAVeryLongName5(
         $firstParameter,
         $secondParameter=array(1,2,3),
         $third=null
     ) {
     }

}

$noArgs_longVars = function () use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};

$longArgs_longVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};

$longArgs_longVars = function ($longArgument,
    $muchLongerArgument) use ($muchLongerVar3
) {
    // body
};

$noArgs_longVars = function () use (
    $longVar1,  $longerVar2,
    $muchLongerVar3
) {
    // body
};

usort(
    $data,
    function ($a, $b) {
        // body
    }
);

function myFunction(
    $firstParameter,$secondParameter=[1,2,3],$third=null
) {
}

if (array_filter(
    $commands,
    function ($cmd) use ($commandName) {
        return ($cmd['name'] == $commandName);
    }
)) {
    // Do something
}

function foo( // comment
    $bar,
    $baz
) { // comment
    // ...
}

function foo($bar = [
    1,
    2,
], $foo)
{
    // body
}

$foo = function ($bar = [
    1,
    2,
]) use ($longVar1, $longerVar2) {
    // body
};

function foo($bar = [
    1,
    2,
],
$foo)
{
    // body
}

function foo(
    $bar = [
        1,
        2,
    ],
    $foo
) {
    // body
}

function foo(
    $param1,

    $param2,

    $param3,
) : SomeClass {
}

// Issue 1959.
function __construct(
    $foo,   // This is foo
    $bar
) {}

function __construct(
    $foo /* this is foo */,
    $bar // this is bar
) {}

function __construct(
    $foo,   // phpcs:ignore Standard.Category.Sniff -- for reasons.
    $bar
) {}

public function hello(string $greeting
) {
}

public function hello(string $greeting // cant fix this
) {
}

public function hello(string $greeting // phpcs:ignore Standard.Category.Sniff -- for reasons.
) {
}

function foo(


    $foo,
    $bar
) {
}

class ConstructorPropertyPromotionSingleLineDocblockIndentOK
{
    public function __construct(
        /** @var string */
        public string $public,
        /** @var string */
        private string $private,
    ) {
    }
}

class ConstructorPropertyPromotionMultiLineDocblockAndAttributeIndentOK
{
    public function __construct(
        /**
         * @var string
         * @Assert\NotBlank()
         */
        public string $public,
        /**
         * @var string
         * @Assert\NotBlank()
         */
        #[NotBlank]
        private string $private,
    ) {
    }
}

class ConstructorPropertyPromotionSingleLineDocblockIncorrectIndent
{
    public function __construct(
      /** @var string */
      public string $public,
          /** @var string */
          private string $private,
    ) {
    }
}

class ConstructorPropertyPromotionMultiLineDocblockAndAttributeIncorrectIndent
{
    public function __construct(
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $public,
/**
 * @var string
 * @Assert\NotBlank()
 */
#[NotBlank]
private string $private,
    ) {
    }
}
