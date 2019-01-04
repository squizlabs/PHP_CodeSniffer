# OpenBraceNewLine

PSR2 indicates:

> Opening braces for classes MUST go on the next line, and closing braces MUST go on the next line after the body.

## Correct

The following example should pass lints, and is the correct way of formatting a class:

```php
class Foo
{
    public function __construct()
    {
        // ...
    }
}
```

The opening brace is placed directly after the `Foo` statement, on the next line.

## Incorrect

### Opening brace on the same line

```php
class Foo{
    public function __construct()
    {
        // ...
    }
}
```

In this case, the opening brace is on the same line as the class declaration. This is not valid according to the PSR2
specification

### Multiple spaces between class and opening brace

```php
class Foo

{
    public function __construct()
    {
        // ...
    }
}
```

In this case, the brace is below the class declaration but not *directly* below it. Accordingly, this is also invalid
according to the PSR2 specification.

## References

- https://www.php-fig.org/psr/psr-2/
