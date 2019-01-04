<?php

$a = true;

// Fixable errors
if ($a) {
	echo true;
}

if ($a)

{
	echo true;
}

// Note the echo here will not be indented by this sniff but by PEAR's MultiLineConditionSniff
if ($a)
{echo true;
}

if ($a)
     {
	echo true;
}

function muh()
{
	if ($a)
		{
		echo true;
	}

	if ($a)
{
		echo true;
	}

	// If statement with spaces which aren't equal to a tab space (4 spaces)
	if ($a)
	  {
		echo true;
	}
}

// NewlineAfterOpenBrace
if ($a)
{

	echo true;
}

while ($a < $b)
{


	echo 'muh';
}

// Closure indentation tests
function foo()
{
	// Closure with control structures
	usort(
		$c,
		function($a, $b) use ($that)
		{
			$o = $that->getState('l.o');

			if (strtolower($that->getState('l.d')) === 'asc')
		{
				echo 'muh';
			}
			else
		{
				echo true;
			}
		}
	);
}

// Anonymous classes test
$this->attachRule(
	new class($this) implements FooInterface
	{
		protected $r;

		public function __construct(RV $r)
		{
			$this->r = $r;
		}

		public function preprocess(&$q)
		{
		}

		public function parse(&$s, &$v)
		{
			// Set fake vars before control structures
			$v = $this->r->getV();
			$a = $this->r->m->getA();

			if ($a){
				$v = array_merge($a->q, $v);
			}

			// Comment before
			if ($b)
{
				return;
			}

			// Get the item
			$s = array_shift($shift);

			if ($c === false)
				{
				throw new InvalidArgumentException('Invalid thing', 42);
			}
			list ($v, $j) = explode('-', $s);

			if (!isset($vs[$v]))
		{
				throw new InvalidArgumentException('Invalid thing two', 84);
			}

			$d = Factory::getD();
			$vr['v'] = $vw;

			// Switch case closure case 1
			switch ($v)
				{
				case 't':
					// Search for t.
					$d->setQ(
						$d->getQ(true)
							->select('t')
							->from('#__c')
							->where('j = ' . (int) $I)
						,
						0,
						1
					);
					$t = (int) $d->loadR();
					$v['t'] = $j;
					break;

				case 'i';
					// Set case.
					$d->setQ(
						$d->getQ(true)
							->select('i')
							->from('#__c')
							->where('j = ' . (int) $I)
						,
						0,
						1
					);
					$i = (int) $d->loadR();
					$v['i'] = $j;
					break;
			}

			return $v;
		}
	}
);

// Round 2 Closure indentation tests
$i = 1;
$func = function ($m) use ($q, $a, &$i)
{
	if (isset($m[6]) && $m[6] === '%')
	{
		return '%';
	}
	// Switch case with closure 2.
	switch ($m[5])
{
		case 't':
			return $q->currentT();
			break;
		case 'z':
			if (true)
				{
				return $q->nullD(true);
			}
			return $query->nullD(false);
			break;
		case 'Z':
			return $query->nullD(true);
			break;
	}
};

class Foo
{
	public function foo($foo)
	{
		$q = $this;
		$a  = \array_slice(\func_get_args(), 1);
		array_unshift($a, null);
		$i    = 1;
		$func = function ($m) use ($q, $a, &$i) {
			if (isset($m[6]) && $m[6] === '%')
		{
				return '%';
			}
		};
	}
}
