<?php
class Foo {
    public const BAR = 'bar';
    const BAZ = 'baz';
}

const APPLICATION_ENV = 'development';

// Issue 3526, PHP 8.1 final constants.
class SampleEnum
{
    final const FOO = 'SAMPLE';

    public final const BAR = 'SAMPLE';

    final private const BAZ = 'SAMPLE';
}

enum SomeEnum {
    public const BAR = 'bar';
    const BAZ = 'baz';
}
