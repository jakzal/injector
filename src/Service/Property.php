<?php
declare(strict_types=1);

namespace Zalas\Injector\Service;

use Zalas\Injector\Service\Exception\MissingServicePropertyException;

final class Property
{
    private string $className;

    private string $propertyName;

    private string $serviceId;

    /**
     * @throws MissingServicePropertyException
     */
    public function __construct(string $className, string $propertyName, string $serviceId)
    {
        if (!\property_exists($className, $propertyName)) {
            throw new MissingServicePropertyException($className, $propertyName);
        }

        $this->propertyName = $propertyName;
        $this->serviceId = $serviceId;
        $this->className = $className;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
