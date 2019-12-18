<?php
declare(strict_types=1);

namespace Zalas\Injector\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use Zalas\Injector\Service\Exception\MissingServiceIdException;
use Zalas\Injector\Service\Extractor;
use Zalas\Injector\Service\Property;

final class ReflectionExtractor implements Extractor
{
    /**
     * @var string[]
     */
    private $ignoredClasses;

    /**
     * @param string[] $ignoredClasses
     */
    public function __construct(array $ignoredClasses = [])
    {
        $this->ignoredClasses = $ignoredClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(string $class): array
    {
        return $this->extractFromReflection(new \ReflectionClass($class));
    }

    private function extractFromReflection(\ReflectionClass $class): array
    {
        $properties = $this->mapClassToServiceProperties($class);
        $parentProperties = $class->getParentClass() ? $this->extractFromReflection($class->getParentClass()) : [];

        return \array_merge($properties, $parentProperties);
    }

    private function mapClassToServiceProperties(\ReflectionClass $class): array
    {
        if (\in_array($class->getName(), $this->ignoredClasses)) {
            return [];
        }

        $docBlockFactory = DocBlockFactory::createInstance(['inject' => Inject::class]);
        $classContext = (new ContextFactory())->createFromReflector($class);

        return \array_filter(\array_map(
            function (\ReflectionProperty $propertyReflection) use ($docBlockFactory, $classContext, $class) {
                $context = $this->getTraitContextIfExists($propertyReflection) ?? $classContext;

                if ($propertyReflection->getDeclaringClass()->getName() === $class->getName()) {
                    return $this->createServiceProperty($propertyReflection, $docBlockFactory, $context);
                }
            },
            $class->getProperties()
        ));
    }

    private function getTraitContextIfExists(\ReflectionProperty $propertyReflection): ?Context
    {
        foreach ($propertyReflection->getDeclaringClass()->getTraits() as $trait) {
            if ($trait->hasProperty($propertyReflection->getName())) {
                return (new ContextFactory())->createFromReflector($trait);
            }
        }

        return null;
    }

    private function createServiceProperty(\ReflectionProperty $propertyReflection, DocBlockFactory $docBlockFactory, Context $context): ?Property
    {
        if (!$propertyReflection->getDocComment()) {
            return null;
        }

        $docBlock = $docBlockFactory->create($propertyReflection, $context);
        $inject = $this->getFirstTag($docBlock, 'inject');

        if (!$inject instanceof Inject) {
            return null;
        }

        $serviceId = $this->getServiceId((string)$inject, $docBlock, $propertyReflection);

        if (empty($serviceId)) {
            throw new MissingServiceIdException($propertyReflection->getDeclaringClass()->getName(), $propertyReflection->getName());
        }

        return new Property($propertyReflection->getDeclaringClass()->getName(), $propertyReflection->getName(), $serviceId);
    }

    private function getServiceId(string $injectId, DocBlock $docBlock, \ReflectionProperty $propertyReflection): ?string
    {
        return $injectId ? $injectId : $this->extractType($docBlock, $propertyReflection);
    }

    private function extractType(DocBlock $docBlock, \ReflectionProperty $propertyReflection): ?string
    {
        return $this->extractTypeFromPropertyReflection($propertyReflection) ?? $this->extractTypeFromDocBlock($docBlock);
    }

    private function extractTypeFromPropertyReflection(\ReflectionProperty $propertyReflection): ?string
    {
        return \PHP_VERSION_ID >= 70400 && $propertyReflection->getType() instanceof \ReflectionType ? $propertyReflection->getType()->getName() : null;
    }

    private function extractTypeFromDocBlock(DocBlock $docBlock): ?string
    {
        $var = $this->getFirstTag($docBlock, 'var');

        return $var instanceof Var_ ? \ltrim((string)$var->getType(), '\\') : null;
    }

    private function getFirstTag(DocBlock $docBlock, string $name): ?Tag
    {
        $tags = $docBlock->getTagsByName($name);

        return $tags[0] ?? null;
    }
}
