<?php

class MyClass extends ParentClass
{
    public const PUBLIC_VALID = 'PUBLIC_VALID';

    protected const PROTECTED_VALID = 'PROTECTED_VALID';

    private const PRIVATE_VALID = 'PRIVATE_VALID';

    const INVALID = 'INVALID';

    // See Squiz.WhiteSpace.ScopeKeywordSpacing.Incorrect
    private
        const VALID_FOR_THIS_SNIFF = 'VALID_FOR_THIS_SNIFF';
}
