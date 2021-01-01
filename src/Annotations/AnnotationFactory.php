<?php

declare(strict_types=1);

namespace NGSOFT\Annotations;

use InvalidArgumentException;
use NGSOFT\{
    Annotations\Tags\TagBasic, Annotations\Types\AnnotationBasic, Annotations\Types\ClassAnnotation,
    Annotations\Types\ConstantAnnotation, Annotations\Types\MethodAnnotation, Annotations\Types\PropertyAnnotation,
    Interfaces\AnnotationFactoryInterface, Interfaces\AnnotationInterface, Interfaces\AnnotationTagInterface
};
use ReflectionClass,
    ReflectionClassConstant,
    ReflectionMethod,
    ReflectionProperty;

class AnnotationFactory implements AnnotationFactoryInterface {

    /** @var array<string,string> */
    protected $annotationClasses = [
        ReflectionClass::class => ClassAnnotation::class,
        ReflectionMethod::class => MethodAnnotation::class,
        ReflectionProperty::class => PropertyAnnotation::class,
        ReflectionClassConstant::class => ConstantAnnotation::class
    ];

    /** @var string */
    protected $defaultTagClass = TagBasic::class;

    /** @var array<string,string> */
    protected $tagClasses = [];

    /**
     * Add a custom Tag
     * @param string $tagName
     * @param string|AnnotationTagInterface $tagClass
     * @return self
     * @throws InvalidArgumentException
     */
    public function addTagName(string $tagName, $tagClass): self {

        if (!preg_match(AnnotationTagInterface::VALID_TAG_NAME_REGEX, $tagName)) {
            throw new InvalidArgumentException(sprintf('Invalid tag name "%s".', $tagName));
        }

        if ($tagClass instanceof AnnotationTagInterface) $tagClass = get_class($tagClass);

        if (
                is_string($tagClass)
                and class_exists($tagClass)
                and ($implements = class_implements($tagClass))
                and in_array(AnnotationTagInterface::class, $implements)
        ) {

            $this->tagClasses[$tagName] = $tagClass;
            return $this;
        }

        throw new InvalidArgumentException('Invalid tag class provided.');
    }

    /**
     * Assert Valid Reflector
     * @param mixed $reflector
     * @throws InvalidArgumentException
     */
    protected function assertValidReflection($reflector) {

        if (
                $reflector instanceof ReflectionClass
                or $reflector instanceof ReflectionProperty
                or $reflector instanceof ReflectionMethod
                or $reflector instanceof ReflectionClassConstant
        ) return;
        throw new InvalidArgumentException('Invalid Reflector Provided.');
    }

    /** {@inheritdoc} */
    public function createAnnotation($reflector, AnnotationTagInterface $tag): AnnotationInterface {
        $this->assertValidReflection($reflector);
        $reflectorClass = get_class($reflector);
        $annotationClass = $this->annotationClasses[$reflectorClass] ?? AnnotationBasic::class;
        return new $annotationClass($reflector, $tag);
    }

    /** {@inheritdoc} */
    public function createTag(string $name, $value = null): AnnotationTagInterface {
        if (!preg_match(AnnotationTagInterface::VALID_TAG_NAME_REGEX, $name)) {
            throw new InvalidArgumentException(sprintf('Invalid tag name "%s".', $name));
        }
        $className = $this->tagClasses[$name] ?? $this->defaultTagClass;
        return (new $className())
                        ->withName($name)
                        ->withValue($value);
    }

}
