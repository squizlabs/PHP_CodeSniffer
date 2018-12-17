<?php declare(strict_types=1);

namespace PHP_CodeSniffer\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;

class ConfigPropertyReflection implements PropertyReflection
{
    /**
     * @var ClassReflection
     */
    private $declaringClassReflection;

    /**
     * @var Type
     */
    private $type;

    public function __construct(ClassReflection $declaringClassReflection, Type $type)
    {
        $this->declaringClassReflection = $declaringClassReflection;
        $this->type = $type;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClassReflection;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function isWritable(): bool
    {
        return true;
    }
}
