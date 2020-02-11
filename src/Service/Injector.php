<?php
declare(strict_types=1);

namespace Zalas\Injector\Service;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionProperty;
use Zalas\Injector\Service\Exception\AmbiguousInjectionDefinitionException;
use Zalas\Injector\Service\Exception\FailedToInjectServiceException;
use Zalas\Injector\Service\Exception\MissingServiceException;

/**
 * Injects services from a PSR-11 service container to the passed object.
 *
 * `ContainerFactory` is used to access the service container.
 * Details of services to inject are extracted with an `Extractor` implementation that's provided by the `ExtractorFactory`.
 */
class Injector
{
    private ContainerFactory $containerFactory;

    private ExtractorFactory $extractorFactory;

    public function __construct(ContainerFactory $containerFactory, ExtractorFactory $extractorFactory)
    {
        $this->containerFactory = $containerFactory;
        $this->extractorFactory = $extractorFactory;
    }

    /**
     * @throws FailedToInjectServiceException
     * @throws MissingServiceException
     */
    public function inject(object $object): void
    {
        \array_map($this->getPropertyInjector($object), $this->validateProperties($this->extractProperties($object)));
    }

    private function getPropertyInjector(object $object): Closure
    {
        $container = $this->containerFactory->create();

        return function (Property $property) use ($object, $container) {
            $this->injectService($property, $object, $container);
        };
    }

    private function injectService(Property $property, object $object, ContainerInterface $container): void
    {
        $reflectionProperty = new ReflectionProperty($property->getClassName(), $property->getPropertyName());
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $this->getService($container, $property));
    }

    /**
     * @return array|Property[]
     * @param object $object
     */
    private function extractProperties(object $object): array
    {
        return $this->extractorFactory->create()->extract(\get_class($object));
    }

    /**
     * @param array|Property[] $properties
     * @return array|Property[]
     */
    private function validateProperties(array $properties): array
    {
        $duplicates = $this->filterDuplicateProperties($properties);

        if (!empty($duplicates)) {
            throw new AmbiguousInjectionDefinitionException(\array_pop($duplicates), \array_pop($duplicates));
        }

        return $properties;
    }

    /**
     * @param array|Property[] $properties
     * @return array|Property[]
     */
    private function filterDuplicateProperties(array $properties): array
    {
        $groupedByName = \array_reduce($properties, function (array $properties, Property $p) {
            $properties[$p->getPropertyName()][] = $p;

            return $properties;
        }, []);
        $duplicates = \array_filter($groupedByName, function (array $properties) {
            if ($this->cannotHaveDuplicates($properties)) {
                return false;
            }

            list($privates, $nonPrivates) = $this->splitOnVisibilityAndMapToClasses($properties);

            $duplicatedPrivateProperty = \count(\array_unique($privates)) !== \count($privates);
            $overriddenOrDuplicatedNonPrivateProperty = \count($nonPrivates) > 1;

            return $duplicatedPrivateProperty || $overriddenOrDuplicatedNonPrivateProperty;
        });

        return \array_shift($duplicates) ?? [];
    }

    /**
     * @param Property[] $properties
     * @return bool
     */
    private function cannotHaveDuplicates(array $properties): bool
    {
        // micro-optimisation: if there's no more than one property there's no point to inspect their visibility.
        // This is why infection ignores OneZeroInteger and LessThanOrEqualTo in this method.

        return \count($properties) <= 1;
    }

    /**
     * @param Property[] $properties
     *
     * @return string[][] tuple of class names with a private (left) or non-private (right) property
     */
    private function splitOnVisibilityAndMapToClasses(array $properties): array
    {
        return \array_reduce($properties, function (array $tuple, Property $p) {
            $tuple[$this->isPrivate($p) ? 0 : 1][] = $p->getClassName();

            return $tuple;
        }, [[], []]);
    }

    private function isPrivate(Property $property): bool
    {
        return (new ReflectionProperty($property->getClassName(), $property->getPropertyName()))->isPrivate();
    }

    private function getService(ContainerInterface $container, Property $property)
    {
        try {
            return $container->get($property->getServiceId());
        } catch (NotFoundExceptionInterface $e) {
            throw new MissingServiceException($property, $e);
        } catch (ContainerExceptionInterface $e) {
            throw new FailedToInjectServiceException($property, $e);
        }
    }
}
