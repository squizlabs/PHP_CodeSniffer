<?php

// This file mimics the situation for php version < 7.1.0
class MyClass extends ParentClass
{
    public const PUBLIC_VALID = 'PUBLIC_VALID';

    protected const PROTECTED_VALID = 'PROTECTED_VALID';

    private const PRIVATE_VALID = 'PRIVATE_VALID';

    const INVALID = 'INVALID';
}
