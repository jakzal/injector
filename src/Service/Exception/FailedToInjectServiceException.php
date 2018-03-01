<?php
declare(strict_types=1);

namespace Zalas\Injector\Service\Exception;

use RuntimeException;
use Zalas\Injector\Service\Exception;
use Zalas\Injector\Service\Property;

final class FailedToInjectServiceException extends RuntimeException implements Exception
{
    public function __construct(Property $property, \Exception $previous = null)
    {
        parent::__construct(
            \sprintf(
                'Failed to inject the `%s` service into `%s::%s`.',
                $property->getServiceId(),
                $property->getClassName(),
                $property->getPropertyName()
            ),
            0,
            $previous
        );
    }
}
