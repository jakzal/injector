<?php
declare(strict_types=1);

namespace Zalas\Injector\Reflection;

use Zalas\Injector\Service\Exception\MissingServiceIdException;
use Zalas\Injector\Service\Extractor;
use Zalas\Injector\Service\Property;

final class ReflectionExtractor implements Extractor
{
    /**
     * @var string[]
     */
    private array $ignoredClasses;

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

        return \array_map([$this, 'createProperty'], $this->filterReflectionPropertiesForInjection($class));
    }

    private function filterReflectionPropertiesForInjection(\ReflectionClass $class): array
    {
        return \array_filter(
            $class->getProperties(),
            function (\ReflectionProperty $property) use ($class) {
                return $property->getDeclaringClass()->getName() === $class->getName()
                    && \is_string($property->getDocComment())
                    && 1 === \preg_match('#\s*\**\s*\@inject#', $property->getDocComment());
            }
        );
    }

    private function extractServiceId(\ReflectionProperty $property): string
    {
        if (1 === \preg_match('#\s*\**\s*\@inject (?P<serviceId>[^\s*]+)#', $property->getDocComment(), $matches)) {
            return $matches['serviceId'];
        } elseif ($property->getType() instanceof \ReflectionType) {
            return $property->getType()->getName();
        }

        throw new MissingServiceIdException($property->getDeclaringClass()->getName(), $property->getName());
    }

    private function createProperty(\ReflectionProperty $property): Property
    {
        return new Property($property->getDeclaringClass()->getName(), $property->getName(), $this->extractServiceId($property));
    }
}
