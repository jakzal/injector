<?php
declare(strict_types=1);

namespace Zalas\Injector\Service\Exception;

use RuntimeException;
use Zalas\Injector\Service\Exception;
use Zalas\Injector\Service\Property;

final class MissingServiceException extends RuntimeException implements Exception
{
    public function __construct(Property $property, \Exception $previous = null)
    {
        parent::__construct(
            \sprintf(
                'The `%s` service cannot be injected into `%s::%s` as it could not be found in the container.',
                $property->getServiceId(),
                $property->getClassName(),
                $property->getPropertyName()
            ),
            0,
            $previous
        );
    }
}
