<?php
function validFunctionReturnOne()
{
    return;
}

function validFunctionReturnTwo()
{

    return;
}

function validFunctionReturnThree()
{
    echo "";

    return;
}

function invalidFunctionReturnOne()
{
    echo "";
    return;
}

switch ($condition) {
    case 'foo':
        return true;

    default:
        return false;
}

