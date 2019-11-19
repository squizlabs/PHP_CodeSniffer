<?php
// Correct.
function Foo() {}
$a = function() {};

abstract class Foo {
	public function PublicFunction() {}
	private function PrivateFunction() {}
	protected function ProtectedFunction() {}
	static function StaticFunction() {}
	abstract protected function AbstractProtectedFunction();
	final static public function FinalStaticPublicFunction() {}
}

// Incorrect.
Function Bar() {}
$a = FUNCTION() {};

abstract class Bar {
	Public function PublicFunction() {}
	Private function PrivateFunction() {}
	Protected function ProtectedFunction() {}
	Static function StaticFunction() {}
	ABSTRACT proTECted FUNCTION AbstractProtectedFunction();
	Final STATIC PUBLIC Function FinalStaticPublicFunction() {}
}

$a = FN($x) => $x;
