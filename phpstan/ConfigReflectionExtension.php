<?php declare(strict_types=1);

namespace PHP_CodeSniffer\PHPStan;

use PHP_CodeSniffer\Config;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;

final class ConfigReflectionExtension implements PropertiesClassReflectionExtension
{

    /**
     * @var Type[]
     */
    private $properties = [];

    public function __construct()
    {
        $this->properties = [
            'files' => new ArrayType(new IntegerType(), new StringType()),
            'standards' => new ArrayType(new IntegerType(), new StringType()),
            'verbosity' => TypeCombinator::union(
                new ConstantIntegerType(0),
                new ConstantIntegerType(1),
                new ConstantIntegerType(2),
                new ConstantIntegerType(3)
            ),
            'interactive' => new BooleanType(),
            'parallel' => new BooleanType(),
            'cache' => new BooleanType(),
            'cacheFile' => new StringType(),
            'colors' => new BooleanType(),
            'explain' => new BooleanType(),
            'local' => new BooleanType(),
            'showSources' => new BooleanType(),
            'showProgress' => new BooleanType(),
            'quiet' => new BooleanType(),
            'annotations' => new BooleanType(),
            'tabWidth' => new IntegerType(),
            'encoding' => new StringType(),
            'sniffs' => new ArrayType(new IntegerType(), new StringType()),
            'exclude' => new ArrayType(new IntegerType(), new StringType()),
            'ignored' => new ArrayType(new IntegerType(), new StringType()),
            'reportFile' => new StringType(),
            'generator' => new StringType(),
            'filter' => new StringType(),
            'bootstrap' => new ArrayType(new IntegerType(), new StringType()),
            'reportWidth' => new IntegerType(),
            'errorSeverity' => new IntegerType(),
            'warningSeverity' => new IntegerType(),
            'recordErrors' => new BooleanType(),
            'suffix' => new StringType(),
            'basepath' => new StringType(),
            'stdin' => new BooleanType(),
            'stdinContent' => new StringType(),
            'stdinPath' => new StringType(),
            'extensions' => new ArrayType(new StringType(), new StringType()),
            'reports' => new ArrayType(new StringType(), TypeCombinator::addNull(new StringType())),
            'unknown' => new ArrayType(new IntegerType(), new StringType()),
        ];
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        return $classReflection->getName() === Config::class && array_key_exists($propertyName, $this->properties);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        return new ConfigPropertyReflection($classReflection, $this->getPropertyType($propertyName));
    }

    private function getPropertyType(string $propertyName): Type
    {
        return $this->properties[$propertyName];
    }
}
