<?php

abstract class IncorrectName // error
{

}

abstract class AbstractCorrectName
{

}

abstract class IncorrectNameAbstract // error
{

}

abstract class InvalidNameabstract // error
{

}

abstract class IncorrectAbstractName // error
{

}

$anon = new class {};

class AbstractClassName
{

}

if (!class_exists('AbstractClassCorrectName')) {
    abstract class AbstractClassCorrectName
    {

    }
}
if (!class_exists('ClassAbstractIncorrectName')) {
    abstract class ClassAbstractIncorrectName // error
    {

    }
}

$abstractVar = '';

$var = 'abstract class IncorrectNameButOk';

$abstracVar = '';

class NameAbstractBar {}

abstract class abstractOkName
{

}