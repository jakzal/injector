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
    /**
     * @var ContainerFactory
     */
    private $containerFactory;

    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    public function __construct(ContainerFactory $containerFactory, ExtractorFactory $extractorFactory)
    {
        $this->containerFactory = $containerFactory;
        $this->extractorFactory = $extractorFactory;
    }

    /**
     * @throws FailedToInjectServiceException
     * @throws MissingServiceException
     * @param object $object
     */
    public function inject(/*object */$object): void
    {
        \array_map($this->getPropertyInjector($object), $this->validateProperties($this->extractProperties($object)));
    }

    private function getPropertyInjector(/*object */$object): Closure
    {
        $container = $this->containerFactory->create();

        return function (Property $property) use ($object, $container) {
            $this->injectService($property, $object, $container);
        };
    }

    private function injectService(Property $property, /*object */$object, ContainerInterface $container): void
    {
        $reflectionProperty = new ReflectionProperty($property->getClassName(), $property->getPropertyName());
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $this->getService($container, $property));
    }

    /**
     * @return array|Property[]
     * @param object $object
     */
    private function extractProperties(/*object */$object): array
    {
        return $this->extractorFactory->create()->extract(\get_class($object));
    }

    /**
     * @param array|Property[] $properties
     * @return array|Property[]
     */
    private function validateProperties(array $properties): array
    {
        $visited = [];

        foreach ($properties as $property) {
            if (!$this->isPrivate($property)) {
                $key = $property->getPropertyName();

                if (isset($visited[$key])) {
                    throw new AmbiguousInjectionDefinitionException($visited[$key], $property);
                }

                $visited[$key] = $property;
            }
        }

        return $properties;
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
