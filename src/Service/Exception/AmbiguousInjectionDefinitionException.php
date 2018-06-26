<?php
declare(strict_types=1);

namespace Zalas\Injector\Service\Exception;

use RuntimeException;
use Zalas\Injector\Service\Exception;
use Zalas\Injector\Service\Property;

final class AmbiguousInjectionDefinitionException extends RuntimeException implements Exception
{
    public function __construct(Property $first, Property $second, \Exception $previous = null)
    {
        parent::__construct(
            \sprintf(
                'Services `%s` and `%s` have been configured to be injected in property `%s::%s`.',
                $first->getServiceId(),
                $second->getServiceId(),
                $first->getClassName(),
                $first->getPropertyName()
            ),
            0,
            $previous
        );
    }
}
