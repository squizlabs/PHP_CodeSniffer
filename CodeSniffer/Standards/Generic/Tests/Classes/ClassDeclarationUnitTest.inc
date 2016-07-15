<?php

// The following are all ok.
interface Test_Interface_Ok_A {
}

class Test_Class_Ok_A {
}

class Test_Class_Ok_B extends Test_Class_Ok_A {
}

class Test_Class_Ok_C implements Test_Interface_Ok_A {
}


// These are all incorrect.
interface Test_Interface_Bad_A
{ // There should be no content after the brace.
}

class Test_Class_Bad_A
	{
	}

class Test_Class_Bad_B extends Test_Class_Bad_A

{ // There should be no content after the brace.
}

class Test_Class_Bad_C implements Test_Interface_Bad_A


{
}

// These should all be flagged for wrong whitespace before opening brace.
interface Test_Interface_Bad_C   {
}

class Test_Class_Bad_G	{		
}

class Test_Class_Bad_H extends Test_Class_Bad_G			{
}

class Test_Class_Bad_I implements Test_Interface_Bad_C{
}

// This one should be flagged as a potential parse error.
class Test_Class_Bad_H
