<?php
declare(strict_types=1);

namespace Zalas\Injector\Service;

use Zalas\Injector\Service\Exception\MissingServicePropertyException;

final class Property
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var bool
     */
    private $privatized;

    /**
     * @throws MissingServicePropertyException
     */
    public function __construct(string $className, string $propertyName, string $serviceId, bool $privatized = false)
    {
        if (!\property_exists($className, $propertyName)) {
            throw new MissingServicePropertyException($className, $propertyName);
        }

        $this->propertyName = $propertyName;
        $this->serviceId = $serviceId;
        $this->className = $className;
        $this->privatized = $privatized;
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

    public function privatized(): bool
    {
        return $this->privatized;
    }
}
